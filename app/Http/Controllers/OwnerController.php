<?php

namespace App\Http\Controllers;
use App\Models\Contact;
use App\Models\ContactArchive;
use App\Models\ContactDiscard;
use App\Models\Owner;

class OwnerController extends Controller{

    public function owner(){
        $owner = Owner::paginate(10);
        return view('Sale_Agent_Page', [
            'owner'=>$owner
        ]);
    }

    public function viewOwner($owner_pid) {
        // Execute the queries to get the actual data
        $editOwner = Owner::where('owner_pid', $owner_pid)->first();
        
        // Get the total contacts count allocated to this owner
        $totalContacts = Contact::where('fk_contacts__owner_pid', $owner_pid)->count();
        
        // Get the count of contacts where status is 'HubSpot'
        $hubspotContactsCount = Contact::where('fk_contacts__owner_pid', $owner_pid)
                                       ->where('status', 'HubSpot Contact')
                                       ->count();
        
        // Get the count of current engaging contact
        $hubspotCurrentEngagingContact = Contact::where('fk_contacts__owner_pid', $owner_pid)
                                                ->where('status', 'InProgress')
                                                ->count();
                                                
        // Update the 'total_hubspot_sync' column in the 'owners' table
        $editOwner->total_hubspot_sync = $hubspotContactsCount;
        $editOwner->total_in_progress = $hubspotCurrentEngagingContact;
        $editOwner->save();
        
        // Get the contacts
        $ownerContacts = Contact::where('fk_contacts__owner_pid', $owner_pid)->get();
        
        $ownerArchive = ContactArchive::where('fk_contact_archives__owner_pid', $owner_pid)->get();
        $ownerDiscard = ContactDiscard::where('fk_contact_discards__owner_pid', $owner_pid)->get();
        
        // Pass the data to the view
        return view('Edit_Owner_Detail_Page', [
            'editOwner' => $editOwner, 
            'totalContacts' => $totalContacts,
            'hubspotContactsCount' => $hubspotContactsCount,
            'hubspotCurrentEngagingContact' => $hubspotCurrentEngagingContact,
            'ownerContacts' => $ownerContacts,
            'ownerArchive' => $ownerArchive,
            'ownerDiscard' => $ownerDiscard
        ]);
    }
    
}