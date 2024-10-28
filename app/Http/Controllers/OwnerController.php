<?php

namespace App\Http\Controllers;

use App\Models\ArchiveActivities;
use App\Models\BU;
use App\Models\BuCountry;
use App\Models\BuCountryBUH;
use App\Models\BUH;
use App\Models\Contact;
use App\Models\ContactArchive;
use App\Models\ContactDiscard;
use App\Models\Country;
use App\Models\Delete_contacts;
use App\Models\Engagement;
use App\Models\EngagementArchive;
use App\Models\Owner;
use App\Models\SaleAgent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class OwnerController extends Controller
{

    public function saleAgent()
    {
        // Get the current authenticated user
        $user = Auth::user();
        Log::info("User : " . $user);

        $contact = Contact::where('fk_contacts__owner_pid', null)->count();
        Log::info("Total of unassigned contacts: " . $contact);

        // Define the query for SaleAgents
        $query = SaleAgent::query();

        // Check if the user is a BUH, Head, or Admin
        if ($user->role === 'BUH') {
            // Get the BUH's country ID
            $buhId = BUH::where('email', $user->email)->first();
            $buCountry = BuCountryBUH::where('buh_id', $buhId->id)->first();
            $bu = BU::where('id', $buCountry->bu_id)->pluck('name');
            Log::info("BUH's Country: " . $buCountry . "Business Unit: " . $bu);

            // Filter sale agents by the BUH's country
            $query->where('bu_country_id', $buCountry->id);
        } else if ($user->role === 'Head') {
            // Filter sale agents by the Head's ID
            $query = DB::table('sale_agent as sa')
                ->join('bu_country as bc', 'sa.bu_country_id', '=', 'bc.id')
                ->join('buh', 'buh.id', '=', 'bc.buh_id')
                ->where('buh.head_id', $user->id)
                ->select('sa.*');  // Select only sale agents

            Log::info("Filtered sale agents by Head ID: " . $user->id);
        }

        // If the user is Admin, no filtering needed, just get all Sale Agents
        $owner = $query->paginate(10);

        // Get HubSpot sales agents
        $hubspotSalesAgents = $this->getHubspotSalesAgents();

        // Return the view with the appropriate data
        return view('Sale_Agent_Page', [
            'owner' => $owner,
            'user' => $user,
            'hubspotSalesAgents' => $hubspotSalesAgents,
            'contact' => $contact,
            'bu' => $bu
        ]);
    }

    protected function getHubspotSalesAgents()
    {
        try {
            $client = new Client();
            $response = $client->request('GET', 'https://api.hubapi.com/crm/v3/owners', [
                'headers' => [
                    'Authorization' => 'Bearer ' . env('HUBSPOT_API_KEY'),
                    'Content-Type'  => 'application/json',
                ],
                'verify' => false
            ]);

            $hubspotSalesAgents = json_decode($response->getBody(), true);

            // Paginate the Hubspot sales agents
            $hubspotSalesAgents = collect($hubspotSalesAgents);

            return $hubspotSalesAgents;
        } catch (\Exception $e) {
            // Handle any errors that occur during the request
            Log::error($e->getMessage());
            return [];
        }
    }

    public function viewSaleAgent($id)
    {
        $owner = SaleAgent::where('id', $id)->first();
        // Execute the queries to get the actual data
        $editOwner = SaleAgent::where('id', $id)->first();

        // Get the total contacts count allocated to this owner
        $totalContacts = Contact::where('fk_contacts__sale_agent_id', $id)->count();
        $totalArchive = ContactArchive::where('fk_contacts__sale_agent_id', $id)->count();
        $totalDiscard = ContactDiscard::where('fk_contacts__sale_agent_id', $id)->count();

        $totalContact = $totalContacts + $totalArchive + $totalDiscard;
        // Get the count of contacts where status is 'HubSpot'
        $hubspotContactsCount = Contact::where('fk_contacts__sale_agent_id', $id)
            ->where('status', 'HubSpot Contact')
            ->count();

        // Get the count of current engaging contact
        $hubspotCurrentEngagingContact = Contact::where('fk_contacts__sale_agent_id', $id)
            ->where('status', 'InProgress')
            ->count();

        // Update the 'total_hubspot_sync' column in the 'owners' table
        $editOwner->total_assign_contacts = $totalContact;
        $editOwner->total_hubspot_sync = $hubspotContactsCount;
        $editOwner->total_in_progress = $hubspotCurrentEngagingContact;
        $editOwner->total_archive_contacts = $totalArchive;
        $editOwner->total_discard_contacts = $totalDiscard;
        $editOwner->save();

        // Get the contacts
        $ownerContacts = Contact::where(
            'fk_contacts__sale_agent_id',
            $id
        )->paginate(50);

        $ownerArchive = ContactArchive::where(
            'fk_contacts__sale_agent_id',
            $id
        )->paginate(50);
        $ownerDiscard = ContactDiscard::where(
            'fk_contacts__sale_agent_id',
            $id
        )->paginate(50);

        $bus = BU::where('name', $owner->business_unit)->get(); // Get all BUs matching the name
        Log::info("BUs: " . $bus);

        $countries = collect(); // Initialize a collection to store all countries for the BU

        foreach ($bus as $bu) {
            $buCountries = BuCountry::where('bu_id', $bu->id)->get(); // Get all BuCountry records for each BU

            foreach ($buCountries as $buCountry) {
                $country = Country::where('id', $buCountry->country_id)->first(); // Retrieve each Country by ID
                if ($country) {
                    $countries->push($country); // Add the country to the collection
                }
            }
        }

        Log::info('All countries for the BU: ' . $countries);

        // Pass the data to the view
        return view('Edit_Owner_Detail_Page', [
            'editOwner' => $editOwner,
            'totalContacts' => $totalContacts,
            'hubspotContactsCount' => $hubspotContactsCount,
            'hubspotCurrentEngagingContact' => $hubspotCurrentEngagingContact,
            'ownerContacts' => $ownerContacts,
            'ownerArchive' => $ownerArchive,
            'ownerDiscard' => $ownerDiscard,
            'owner' => $owner,
            'countries' => $countries
        ]);
    }

    public function updateSaleAgent(Request $request, $id)
    {

        $owner = SaleAgent::find($id);

        $owner->update([
            $owner->business_unit = $request->input('business_unit'),
            $owner->nationality = $request->input('country')
        ]);

        return redirect()->route('buh#view-sale-agent', ['id' => $id])->with('success', 'Sale Agent updated successfully.');
    }

    public function viewContact($contact_pid)
    {
        /* Retrieve the contact record with the specified 'contact_pid' and pass
         it to the 'Edit_Contact_Detail_Page' view for editing. */

        // Retrieve the contact record with the specified 'contact_pid'
        $editContact = Contact::where('contact_pid', $contact_pid)->first();

        // Check if the contact exists
        if (!$editContact) {
            return redirect()->route('contacts-listing')->with('error', 'Contact not found.');
        }

        // Retrieve the authenticated user
        $user = Auth::user();
        Log::info('User email:' . $user->email);
        $owner = BUH::where('email', $user->email)->first();

        // Retrieve all engagements for the contact
        $engagements = Engagement::where('fk_engagements__contact_pid', $contact_pid)->get();

        // Decrypt images in engagements
        foreach ($engagements as $engagement) {
            if ($engagement->attachments) {
                try {
                    // Decrypt the attachment and base64 encode it for browser display
                    $attachmentsArray = json_decode($engagement->attachments, true); // Decode JSON to array if stored as JSON
                    foreach ($attachmentsArray as &$attachment) {
                        $attachment = 'data:image/jpeg;base64,' . base64_encode(Crypt::decrypt($attachment));
                    }
                    // Convert array back to JSON for the frontend if needed
                    $engagement->attachments = json_encode($attachmentsArray);
                } catch (\Exception $e) {
                    // Handle the case where decryption fails
                    $engagement->attachments = null;
                    Log::error('Failed to decrypt attachment for engagement ID: ' . $engagement->id . ' Error: ' . $e->getMessage());
                }
            }
        }

        // Retrieve engagements archived for the contact
        $engagementsArchive = EngagementArchive::where('fk_engagement_archives__contact_archive_pid', $contact_pid)->get();
        $deletedEngagement = ArchiveActivities::where('fk_engagements__contact_pid', $contact_pid)->get();
        // Use the first engagement for updates if available
        $updateEngagement = $engagements->first();

        // Pass data to the view
        return view('Edit_Contact_Detail_Page')->with([
            'user' => $user,
            'owner' => $owner,
            'editContact' => $editContact,
            'engagements' => $engagements,
            'updateEngagement' => $updateEngagement,
            'engagementArchive' => $engagementsArchive,
            'deletedEngagement' => $deletedEngagement
        ]);
    }
}
