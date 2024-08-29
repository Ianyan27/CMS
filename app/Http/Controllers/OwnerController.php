<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\ContactArchive;
use App\Models\ContactDiscard;
use App\Models\Owner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OwnerController extends Controller{

    public function owner(){
        // Get the current authenticated user
        $user = Auth::user();
        // Check if the user is a BUH or Admin
        if ($user->role == 'BUH') {
            // If the user is BUH, filter owners by the BUH's fk_buh
            $owner = Owner::where('fk_buh', $user->id)->paginate(10);
        } else {
            // If the user is Admin, show all owners
            $owner = Owner::paginate(10);
        }
        // Return the view with the appropriate data
        return view('Sale_Agent_Page', [
            'owner' => $owner, 'user' => $user
        ]);
    }

    public function viewOwner($owner_pid){
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
        $ownerContacts = Contact::where(
            'fk_contacts__owner_pid',
            $owner_pid
        )->paginate(50);

        $ownerArchive = ContactArchive::where(
            'fk_contact_archives__owner_pid',
            $owner_pid
        )->paginate(50);
        $ownerDiscard = ContactDiscard::where(
            'fk_contact_discards__owner_pid',
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
            'ownerDiscard' => $ownerDiscard
        ]);
    }

    public function updateOwner(Request $request, $owner_pid){

        $owner = Owner::find($owner_pid);

        $owner->update([
            $owner->owner_business_unit = $request->input('business_unit'),
            $owner->country = $request->input('country')
        ]);

        return redirect()->route('owner#view_owner', ['owner_pid' => $owner_pid])->with('success', 'Owner updated successfully.');
    }
}
