<?php

namespace App\Http\Controllers;

use App\Models\BU;
use App\Models\BuCountry;
use App\Models\Contact;
use App\Models\ContactArchive;
use App\Models\ContactDiscard;
use App\Models\Country;
use App\Models\SaleAgent;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SaleAgentsController extends Controller
{
    public function saleAgent(){
        // Get the current authenticated user
        $user = Auth::user();
        // Check if the user is a BUH or Admin
        if ($user->role == 'BUH') {
            // If the user is BUH, filter owners by the BUH's fk_buh
            $owner = SaleAgent::where('fk_buh', $user->id)->paginate(10);
            $contact = Contact::where('fk_contacts__owner_pid', null)->count();
            // $archiveContact = ContactArchive::where('fk_contact_archives__owner_pid', null)->count();
            // $discardContact = ContactDiscard::where('fk_contact_discards__owner_pid', null)->count();
            Log::info("Total of unassigned contacts: " . $contact);
        }else if ($user->role == 'Head'){
            $owner = DB::table('sale_agent as sa')
            ->join('bu_country as bc', 'sa.bu_country_id', '=', 'bc.id')
            ->join('buh', 'buh.id', '=', 'bc.buh_id')
            ->where('buh.head_id', $user->id)  // Filter by the head's ID
            ->select('sa.*')  // Select sale agents only
            ->paginate(10);  // Paginate results

            $contact = Contact::where('fk_contacts__owner_pid', null)->count();
            // Log the count of unassigned contacts for Head role
            Log::info("Total of unassigned contacts: " . $contact);
        }
        else {
            // If the user is Admin, show all owners
            $owner = SaleAgent::paginate(10);
            $contact = Contact::get();
            $bu = BU::get();
        }

        // Get Hubspot sales agents
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
    public function viewSaleAgent($id){
        $owner = SaleAgent::where('id', $id)->first();
        // Execute the queries to get the actual data

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
        $owner->total_assign_contacts = $totalContact;
        $owner->total_hubspot_sync = $hubspotContactsCount;
        $owner->total_in_progress = $hubspotCurrentEngagingContact;
        $owner->total_archive_contacts = $totalArchive;
        $owner->total_discard_contacts = $totalDiscard;
        $owner->save();

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

        $saleAgent = SaleAgent::find($id);

        $saleAgent->update([
            $saleAgent->business_unit = $request->input('business_unit'),
            $saleAgent->nationality = $request->input('country')
        ]);

        return redirect()->route(
            Auth::check() && Auth::user()->role == 'Admin' ? 'admin#view-sale-agent' : 'buh#view-sale-agent', 
            ['id' => $id]
        )->with('success', 'Sale Agent updated successfully.');
        
        // return redirect()->route('buh#view-sale-agent', ['id' => $id])->with('success', 'Sale Agent updated successfully.');
    }

}
