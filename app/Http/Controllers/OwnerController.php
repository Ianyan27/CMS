<?php

namespace App\Http\Controllers;
use App\Models\Contact;
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
        $editOwner = Owner::where('owner_pid', $owner_pid)->first(); // Use ->first() or ->get() depending on your needs
        $ownerContacts = Contact::where('fk_contacts__owner_pid', $owner_pid)->get(); // Use ->get() to retrieve all matching records
        
        // Pass the data to the view
        return view('Edit_Owner_Detail_Page', [
            'editOwner' => $editOwner, 
            'ownerContacts' => $ownerContacts
        ]);
    }
    
}