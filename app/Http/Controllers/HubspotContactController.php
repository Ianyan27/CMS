<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Contact;
use GuzzleHttp\Client;
use App\Models\Engagement;

class HubspotContactController extends Controller
{
    public function submitHubspotContacts(Request $request)
    {
        $selectedContacts = $request->input('selectedContacts');
        log::info($selectedContacts);

        if ($selectedContacts) {
            // Retrieve contacts with the selected IDs
            $contacts = Contact::whereIn('contact_pid', $selectedContacts)->get();

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
                    ],
                ];
            })->toArray();

            // Structure the data to match HubSpot's API expectations
            $data = [
                'inputs' => $hubspotContacts,
            ];

            // Log the JSON result (optional)
            Log::info('Selec:ted contacts for HubSpot JSON ' . json_encode($data));

            // Send data to HubSpot
            $client = new Client();
            $response = $client->post('https://api.hubapi.com/crm/v3/objects/contacts/batch/create', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . env('HUBSPOT_API_KEY'), // Ensure your HubSpot API key is stored in your .env file
                ],
                'json' => $data,
                'verify' => false, // Disable SSL verification (use only for testing purposes)
            ]);

            // Check the response from HubSpot
            if (in_array($response->getStatusCode(), [200, 201, 202])) { // 200 OK, 201 Created, 202 Accepted
                // Parse the response to get the HubSpot contact IDs
                $responseBody = json_decode($response->getBody(), true);
                $hubspotContactMap = [];
                foreach ($responseBody['results'] as $result) {
                    $localContact = $contacts->firstWhere('email', $result['properties']['email']);
                    $hubspotContactMap[$localContact->id] = $result['id'];
                }

                // Now create activities based on engagements
                foreach ($contacts as $contact) {
                    $hubspotContactId = $hubspotContactMap[$contact->id];

                    // Retrieve engagements for each contact
                    $engagements = Engagement::where('contact_id', $contact->id)->get();

                    // Format engagements for HubSpot API
                    $activities = $engagements->map(function ($engagement) use ($hubspotContactId) {
                        return [
                            'engagement' => [
                                'type' =>  strtoupper($engagement->activity_name), // 'CALL', 'EMAIL', etc.
                                'timestamp' => $engagement->updated_at, // Time of the activity
                            ],
                            'associations' => [
                                'contactIds' => [$hubspotContactId],
                            ],
                            'metadata' => [
                                "html" => "<div>$engagement->details</div>",
                                "text" => $engagement->details
                            ],
                        ];
                    })->toArray();

                    // Send activities to HubSpot
                    if (!empty($activities)) {
                        $client->post('https://api.hubapi.com/engagements/v1/engagements/batch/create', [
                            'headers' => [
                                'Content-Type' => 'application/json',
                                'Authorization' => 'Bearer ' . env('HUBSPOT_API_KEY'),
                            ],
                            'json' => $activities,
                            'verify' => false,
                        ]);
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
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to submit contacts to HubSpot.',
                ], $response->getStatusCode());
            }
        } else {
            // Handle the case where no contacts were selected
            return response()->json([
                'success' => false,
                'message' => 'No contacts selected.',
            ]);
        }
    }
}
