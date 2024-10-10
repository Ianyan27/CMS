<?php

namespace App\Http\Controllers;

use App\Models\ArchiveActivities;
use App\Models\BuCountry;
use App\Models\BUH;
use App\Models\Contact;
use App\Models\ContactArchive;
use App\Models\ContactDiscard;
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

class OwnerController extends Controller
{

    public function saleAgent()
    {
        // Get the current authenticated user
        $user = Auth::user();
        Log::info("User : " . $user);

        // Check if the user is a BUH or Admin
        if ($user->role === 'BUH') {

            // getting bu country id
            $buhId = BUH::where("email", $user->email)->get()->first();
            $buCountry = BuCountry::where('buh_id', $buhId->id)
                ->first();
            Log::info("bu country: " . $buCountry);
            // If the user is BUH, filter owners by the BUH's fk_buh
            $owner = SaleAgent::where('bu_country_id', $buCountry->id)->paginate(10);

            Log::info("user log: " . $user);
            $contact = Contact::where('fk_contacts__owner_pid', null)->count();
            // $archiveContact = ContactArchive::where('fk_contact_archives__owner_pid', null)->count();
            // $discardContact = ContactDiscard::where('fk_contact_discards__owner_pid', null)->count();
            Log::info("Total of unassigned contacts: " . $contact);
        } else {
            // If the user is Admin, show all owners
            $owner = SaleAgent::paginate(10);
            $contact = Contact::get();
        }

        // Get Hubspot sales agents
        $hubspotSalesAgents = $this->getHubspotSalesAgents();

        // Return the view with the appropriate data
        return view('Sale_Agent_Page', [
            'owner' => $owner,
            'user' => $user,
            'hubspotSalesAgents' => $hubspotSalesAgents,
            'contact' => $contact
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

    public function viewSaleAgent($owner_pid)
    {
        $owner = SaleAgent::where('id', $owner_pid)->first();
        // Execute the queries to get the actual data
        $editOwner = SaleAgent::where('id', $owner_pid)->first();

        // Get the total contacts count allocated to this owner
        $totalContacts = Contact::where('fk_contacts__sale_agent_id', $owner_pid)->count();
        $totalArchive = ContactArchive::where('fk_contacts__sale_agent_id', $owner_pid)->count();
        $totalDiscard = ContactDiscard::where('fk_contacts__sale_agent_id', $owner_pid)->count();

        $totalContact = $totalContacts + $totalArchive + $totalDiscard;
        // Get the count of contacts where status is 'HubSpot'
        $hubspotContactsCount = Contact::where('fk_contacts__owner_pid', $owner_pid)
            ->where('status', 'HubSpot Contact')
            ->count();

        // Get the count of current engaging contact
        $hubspotCurrentEngagingContact = Contact::where('fk_contacts__owner_pid', $owner_pid)
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
            $owner_pid
        )->paginate(50);

        $ownerArchive = ContactArchive::where(
            'fk_contacts__sale_agent_id',
            $owner_pid
        )->paginate(50);
        $ownerDiscard = ContactDiscard::where(
            'fk_contacts__sale_agent_id',
            $owner_pid
        )->paginate(50);

        // Pass the data to the view
        return view('Edit_Owner_Detail_Page', [
            'editOwner' => $editOwner,
            'totalContacts' => $totalContacts,
            'hubspotContactsCount' => $hubspotContactsCount,
            'hubspotCurrentEngagingContact' => $hubspotCurrentEngagingContact,
            'ownerContacts' => $ownerContacts,
            'ownerArchive' => $ownerArchive,
            'ownerDiscard' => $ownerDiscard,
            'owner' => $owner
        ]);
    }

    public function updateSaleAgent(Request $request, $id)
    {

        $owner = SaleAgent::find($id);

        $owner->update([
            $owner->business_unit = $request->input('business_unit'),
            $owner->nationality = $request->input('country')
        ]);

        return redirect()->route('owner#view-owner', ['owner_pid' => $id])->with('success', 'Sale Agent updated successfully.');
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
