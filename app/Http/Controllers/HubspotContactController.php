<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Contact;
use GuzzleHttp\Client;
use App\Models\Engagement;
use Illuminate\Support\Facades\DB;

class HubspotContactController extends Controller
{
    public function submitHubspotContacts(Request $request)
    {
        $selectedContacts = $request->input('selectedContacts');
        Log::info($selectedContacts);

        if (!$selectedContacts) {
            return response()->json([
                'success' => false,
                'message' => 'No contacts selected.',
            ]);
        }

        // Retrieve contacts with the selected IDs and join with the owners table to get the HubSpot owner ID
        $contacts = Contact::whereIn('contact_pid', $selectedContacts)
            ->join('sale_agent', 'contacts.fk_contacts__sale_agent_id', '=', 'sale_agent.id')
            ->select('contacts.*', 'sale_agent.hubspot_id')
            ->get();

        // Convert the retrieved contacts to an array suitable for HubSpot
        $hubspotContacts = $contacts->map(function ($contact) {
            return [
                'properties' => [
                    'firstname' => $contact->name,
                    'email' => $contact->email,
                    'phone' => $contact->contact_number,
                    'address' => $contact->address,
                    'country' => $contact->country,
                    'qualification' => $contact->qualification,
                    'jobtitle' => $contact->job_role,
                    'company' => $contact->company_name,
                    'skills' => $contact->skills,
                    'social_profile' => $contact->social_profile,
                    'hubspot_owner_id' => $contact->owner_hubspot_id, // Include the HubSpot owner ID
                ],
            ];
        })->toArray();

        $data = ['inputs' => $hubspotContacts];

        Log::info('Selected contacts for HubSpot JSON: ' . json_encode($data));

        $client = new Client();
        $response = $client->post('https://api.hubapi.com/crm/v3/objects/contacts/batch/create', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . env('HUBSPOT_API_KEY'),
            ],
            'json' => $data,
            'verify' => false,
        ]);

        if (!in_array($response->getStatusCode(), [200, 201, 202])) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit contacts to HubSpot.',
            ], $response->getStatusCode());
        }

        // Parse the response to get the HubSpot contact IDs
        $responseBody = json_decode($response->getBody(), true);
        $hubspotContactMap = [];

        foreach ($responseBody['results'] as $result) {
            $localContact = $contacts->firstWhere('email', $result['properties']['email']);
            if ($localContact) {
                $hubspotContactMap[$localContact->contact_pid] = $result['id'];
            }
        }

        // Create activities based on engagements
        foreach ($contacts as $contact) {
            $hubspotContactId = $hubspotContactMap[$contact->contact_pid] ?? null;
            if (!$hubspotContactId) {
                Log::warning("HubSpot ID not found for contact ID {$contact->contact_pid}");
                continue;
            }

            $engagements = Engagement::where('fk_engagements__contact_pid', $contact->contact_pid)->get();

            if ($engagements->isEmpty()) {
                Log::warning("No engagements found for contact ID {$contact->contact_pid}");
                continue;
            }

            foreach ($engagements as $engagement) {
                $engagementType = strtoupper($engagement->activity_name);
                if ($engagementType == "PHONE") {
                    $engagementType = "CALL";
                }

                // Retrieve owner details for the email engagement
                $owner = DB::table('contacts as c')
                    ->join('sale_agent as o', 'c.fk_contacts__sale_agent_id', '=', 'o.id')
                    ->where('c.contact_pid', $contact->contact_pid)
                    ->select('o.name', 'o.email')
                    ->first();

                //for whatsapp activity
                if ($engagementType == "WHATSAPP") {
                    $activity = [
                        "properties" => [
                            "hs_communication_channel_type" => "WHATS_APP",
                            "hs_communication_logged_from" => "CRM",
                            "hs_communication_body" => $engagement->details,
                            "hs_timestamp" => strtotime($engagement->created_at) * 1000
                        ],
                        "associations" => [
                            [
                                "to" => [
                                    "id" => $hubspotContactId
                                ],
                                "types" => [
                                    [
                                        "associationCategory" => "HUBSPOT_DEFINED",
                                        "associationTypeId" => 81
                                    ]
                                ]
                            ]
                        ]
                    ];

                    $activityResponse = $client->post('https://api.hubspot.com/crm/v3/objects/communications', [
                        'headers' => [
                            'Content-Type' => 'application/json',
                            'Authorization' => 'Bearer ' . env('HUBSPOT_API_KEY'),
                        ],
                        'body' => json_encode($activity),
                        'verify' => false,
                    ]);

                    $activityResponseBody = json_decode($activityResponse->getBody(), true);
                    Log::info('Activity creation response: ' . json_encode($activityResponseBody));

                    if ($activityResponse->getStatusCode() !== 200) {
                        Log::error('Failed to create activity for contact ID: ' . $contact->contact_pid);
                    }
                }
                //for MEETING and CAll activities
                elseif ($engagementType == "MEETING" || $engagementType == "CALL") {
                    $activity = [
                        "engagement" => [
                            "type" =>  $engagementType,
                            "timestamp" => strtotime($engagement->created_at) * 1000
                        ],
                        "metadata" => [
                            "body" => $engagement->details
                        ],
                        "associations" => [
                            "contactIds" => [$hubspotContactId]
                        ],
                    ];

                    $activityResponse = $client->post('https://api.hubapi.com/engagements/v1/engagements/', [
                        'headers' => [
                            'Content-Type' => 'application/json',
                            'Authorization' => 'Bearer ' . env('HUBSPOT_API_KEY'),
                        ],
                        'body' => json_encode($activity),
                        'verify' => false,
                    ]);

                    $activityResponseBody = json_decode($activityResponse->getBody(), true);
                    Log::info('Activity creation response: ' . json_encode($activityResponseBody));

                    if ($activityResponse->getStatusCode() !== 200) {
                        Log::error('Failed to create activity for contact ID: ' . $contact->contact_pid);
                    }
                }
                //for EMAIL activities
                elseif ($engagementType == "EMAIL") {
                    $activity = [
                        "engagement" => [
                            "type" => "EMAIL",
                            "timestamp" => strtotime($engagement->created_at) * 1000
                        ],
                        "metadata" => [
                            "subject" => "Connected Via Mail",
                            "text" => $engagement->details,
                            "from" => [
                                "email" => $owner->email,
                                "firstName" => $owner->name
                            ]
                        ],
                        "associations" => [
                            "contactIds" => [$hubspotContactId]
                        ],
                    ];
                    $activityResponse = $client->post('https://api.hubapi.com/engagements/v1/engagements/', [
                        'headers' => [
                            'Content-Type' => 'application/json',
                            'Authorization' => 'Bearer ' . env('HUBSPOT_API_KEY'),
                        ],
                        'body' => json_encode($activity),
                        'verify' => false,
                    ]);

                    $activityResponseBody = json_decode($activityResponse->getBody(), true);
                    Log::info('Activity creation response: ' . json_encode($activityResponseBody));

                    if ($activityResponse->getStatusCode() !== 200) {
                        Log::error('Failed to create activity for contact ID: ' . $contact->contact_pid);
                    }
                }
                //DEFAULT 
                else {
                    $activity = [
                        "engagement" => [
                            "type" => "NOTE",
                            "timestamp" => strtotime($engagement->created_at) * 1000
                        ],
                        "metadata" => [
                            "text" => " $engagement->details",
                            "body" => $engagement->details
                        ],
                        "associations" => [
                            "contactIds" => [$hubspotContactId]
                        ],
                    ];
                    $activityResponse = $client->post('https://api.hubapi.com/engagements/v1/engagements/', [
                        'headers' => [
                            'Content-Type' => 'application/json',
                            'Authorization' => 'Bearer ' . env('HUBSPOT_API_KEY'),
                        ],
                        'body' => json_encode($activity),
                        'verify' => false,
                    ]);

                    $activityResponseBody = json_decode($activityResponse->getBody(), true);
                    Log::info('Activity creation response: ' . json_encode($activityResponseBody));

                    if ($activityResponse->getStatusCode() !== 200) {
                        Log::error('Failed to create activity for contact ID: ' . $contact->contact_pid);
                    }
                }
            }
        }

        // Update the contacts with the datetime of HubSpot sync
        foreach ($contacts as $contact) {
            $contact->datetime_of_hubspot_sync = now();
            $contact->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Contacts and activities submitted to HubSpot successfully.',
        ]);
    }
}
