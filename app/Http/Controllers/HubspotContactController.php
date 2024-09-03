<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Contact;
use GuzzleHttp\Client;

class HubspotContactController extends Controller{
    public function submitHubspotContacts(Request $request){
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
                        'phone' => $contact->phone,
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
            Log::info('Selected contacts for HubSpot JSON: ' . json_encode($data));

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
                // Update the contacts with the datetime of Hubspot sync
                foreach ($contacts as $contact) {
                    $contact->datetime_of_hubspot_sync = now(); // assuming the column name is datetime_of_hubspot_sync
                    $contact->save();
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Contacts submitted to HubSpot successfully.',
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
