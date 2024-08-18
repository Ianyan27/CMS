<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ContactController extends Controller
{

    public function contacts(){
        // Get contacts from model
        $contacts = Contact::paginate(10);

        // Pass data to view
        return view('Contact_Listing', ['contacts' => $contacts]);
    }

    public function viewContact($contact_pid){
        /* Retrieve the contact record with the specified 'contact_pid' and pass
         it to the 'Edit_Contact_Detail_Page' view for editing. */
        $editContact = Contact::where('contact_pid', $contact_pid)->first();
        return view('Edit_Contact_Detail_Page')->with(['editContact' => $editContact]);
    }

    public function saveContact(Request $request, $contact_pid){
        /* Find the contact record using the given 'contact_pid', update its attributes
        with the data from the request, and save the changes to the database. 
        After saving, redirect to the contact view page with the updated 'contact_pid'.*/
        $contact = Contact::find($contact_pid);
        $contact->name = $request->input('name');
        $contact->email = $request->input('email');
        $contact->contact_number = $request->input('contact_number');
        $contact->address = $request->input('address');
        $contact->country = $request->input('country');
        $contact->qualification = $request->input('qualification');
        $contact->job_role = $request->input('job_role');
        $contact->skills = $request->input('skills');
        $contact->save();
        return redirect()->route('contact#view', ['contact_pid' => $contact_pid]);
    }
}
