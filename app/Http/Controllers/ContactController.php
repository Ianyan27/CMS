<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\ContactArchive;
use App\Models\ContactDiscard;
use App\Models\Engagement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ContactController extends Controller
{

    public function contacts(){
        // Get contacts from model
        $contacts = Contact::paginate(50);
        $contactArchive = ContactArchive::paginate(50);
        $contactDiscard = ContactDiscard::paginate(50);
        // Pass data to view
        return view('Contact_Listing', ['contacts' => $contacts, 
        'contactArchive' => $contactArchive, 'contactDiscard' => $contactDiscard]);
    }

    public function viewContact($contact_pid){
        /* Retrieve the contact record with the specified 'contact_pid' and pass
         it to the 'Edit_Contact_Detail_Page' view for editing. */
        $editContact = Contact::where('contact_pid', $contact_pid)->first();
        $engagement = Engagement::where('fk_engagements__contact_pid', $contact_pid)->get();
        return view('Edit_Contact_Detail_Page')->with
        (['editContact' => $editContact, 'engagement' => $engagement]);
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

    public function saveActivity(Request $request, $contact_pid){

        $validator = Validator::make($request->all(), [
            'activity-date' => 'required',
            'activity-name' => 'required',
            'activity-details' => 'required',
            'activity-attachments' => 'required|file'
        ]);

        if ($validator->fails()){
            return back()->withErrors($validator)->withInput();
        }

        $engagement = new Engagement();

        if ($request->hasFile('activity-attachments')){
            $imageFile = $request->file('activity-attachments');
            $imageName = uniqid() . '_' . $imageFile->getClientOriginalName();
            $imageFile->move(public_path('/attachments'), $imageName);
            $engagement->attachments = json_encode([$imageName]); // Save as a JSON array
        }

        $engagement->date = $request->input('activity-date');
        $engagement->details = $request->input('activity-details');
        $engagement->activity_name = $request->input('activity-name');
        $engagement->date = $request->input('activity-date');
        $engagement->fk_engagements__contact_pid = $request->input('contact_pid');
        $engagement->save();

        return redirect()->route('contact#view', ['contact_pid'=> $contact_pid]);
    }
}
