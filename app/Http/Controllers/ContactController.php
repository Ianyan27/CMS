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

    public function view_contact($contact_pid){
        $editContact = Contact::where('contact_pid', $contact_pid)->first();
        return view('Edit_Contact_Detail_Page')->with(['editContact' => $editContact]);
    }

    public function save_contact(Request $request, $contact_pid){
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
