<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\ContactArchive;
use App\Models\ContactDiscard;
use App\Models\Engagement;
use App\Models\EngagementArchive;
use App\Models\EngagementDiscard;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ContactController extends Controller
{

    public function contacts()
    {
        // Get contacts from model
        $contacts = Contact::paginate(50);
        $contactArchive = ContactArchive::paginate(50);
        $contactDiscard = ContactDiscard::paginate(50);
        // Pass data to view
        return view('Contact_Listing', [
            'contacts' => $contacts,
            'contactArchive' => $contactArchive,
            'contactDiscard' => $contactDiscard
        ]);
    }

    public function viewContact($contact_pid){
        /* Retrieve the contact record with the specified 'contact_pid' and pass
         it to the 'Edit_Contact_Detail_Page' view for editing. */
        $editContact = Contact::where('contact_pid', $contact_pid)->first();
        $engagements = Engagement::where('fk_engagements__contact_pid', $contact_pid)->get();
        $updateEngagement = $engagements->first();
        return view('Edit_Contact_Detail_Page')->with([
            'editContact' => $editContact,
            'engagements' => $engagements,
            'updateEngagement' => $updateEngagement
        ]);
    }

public function updateContact(Request $request, $contact_pid)
{
    // Find the contact based on the contact_pid
    $contact = Contact::find($contact_pid);

    // Check if the contact exists
    if (!$contact) {
        return redirect()->route('contact-listing')->with('error', 'Contact not found.');
    }
    
    // Handle the "Archive" and "Discard" status cases
    if (in_array($request->input('status'), ['Archive', 'Discard'])) {
        // Determine the target model based on the status
        $targetContactModel = $request->input('status') === 'Archive' ? new ContactArchive() : new ContactDiscard();   
        // Copy the contact data to the new table
        $targetContactModel->fill($contact->toArray());
        $targetContactModel->status = $request->input('status'); // Explicitly set the status
        $targetContactModel->save();

        // Get the ID of the newly created contact in the archive/discard table
        $targetContactId = $request->input('status') === 'Archive' ? $targetContactModel->contact_archive_pid : $targetContactModel->contact_discard_pid;

        // Move related activities
        $activities = Engagement::where('fk_engagements__contact_pid', $contact_pid)->get();
        $targetActivityModel = $request->input('status') === 'Archive' ? new EngagementArchive() : new EngagementDiscard();

        foreach ($activities as $activity) {
            $newActivity = $targetActivityModel->newInstance(); // Create a new instance for each activity
            $newActivity->fill($activity->toArray());

            // Set the foreign key to reference the newly created contact
            if ($request->input('status') === 'Archive') {
                $newActivity->fk_engagement_archives__contact_archive_pid = $targetContactId;
            } else {
                $newActivity->fk_engagement_discards__contact_discard_pid = $targetContactId;
            }

            $newActivity->save();
        }

        // Delete the contact from the current table
        $contact->delete();
        // Delete the engagement activities from the current table
        Engagement::where('fk_engagements__contact_pid', $contact_pid)->delete();

        // Redirect with a success message
        return redirect()->route('contact-listing')->with('success', 'Contact and activities moved to ' . $request->input('status') . ' successfully.');
    }

    // If status is not "Archive" or "Discard", update the contact normally
    $contact->update([
        'name' => $request->input('name'),
        'email' => $request->input('email'),
        'contact_number' => $request->input('contact_number'),
        'address' => $request->input('address'),
        'country' => $request->input('country'),
        'qualification' => $request->input('qualification'),
        'job_role' => $request->input('job_role'),
        'skills' => $request->input('skills'),
        'status' => $request->input('status')
    ]);

    // Redirect with a success message
    return redirect()->route('contact#view', [
        'contact_pid' => $contact_pid
    ])->with('success', 'Contact updated successfully.');
}


    public function saveActivity(Request $request, $contact_pid)
    {

        $validator = Validator::make($request->all(), [
            'activity-date' => 'required',
            'activity-name' => 'required',
            'activity-details' => 'required',
            'activity-attachments' => 'required|file'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $engagement = new Engagement();

        if ($request->hasFile('activity-attachments')) {
            $imageFile = $request->file('activity-attachments');
            $imageName = uniqid() . '_' . $imageFile->getClientOriginalName();
            $imageFile->move(public_path('/attachments/leads'), $imageName);
            $engagement->attachments = json_encode([$imageName]); // Save as a JSON array
        }

        $engagement->date = $request->input('activity-date');
        $engagement->details = $request->input('activity-details');
        $engagement->activity_name = $request->input('activity-name');
        $engagement->fk_engagements__contact_pid = $request->input('contact_pid');
        $engagement->save();

        return redirect()->route('contact#view', ['contact_pid' => $contact_pid])->with('success', 'Activity added successfully.');
    }

    public function editActivity($fk_engagements__contact_pid, $activity_id)
    {
        // Fetch all activities related to the contact ID
        $updateEngagements = Engagement::where('fk_engagements__contact_pid', $fk_engagements__contact_pid)->get();

        // Check if a specific activity exists with the given activity ID
        $activity = $updateEngagements->where('id', $activity_id)->first();

        if (!$activity) {
            return redirect()->route('contact#view', ['contact_pid' => $fk_engagements__contact_pid])
                ->with('error', 'Activity not found.');
        }

        // Redirect back to the contact view with the specific activity ID
        return redirect()->route('contact#view', [
            'contact_pid' => $fk_engagements__contact_pid,
            'updateEngagement' => $activity_id
        ])->with([
            'updateEngagement' => $updateEngagements,
            'activity' => $activity
        ]);
    }

    public function saveUpdateActivity(Request $request, $contact_pid, $activity_id)
    {
        // Validate the input data
        $validator = Validator::make($request->all(), [
            'activity-date' => 'required|date',
            'activity-name' => 'required|string',
            'activity-details' => 'required|string',
            'activity-attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,bmp,gif,svg,pdf'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Retrieve the engagement record to be updated
        $engagement = Engagement::where('fk_engagements__contact_pid', $contact_pid)
            ->where('engagement_pid', $activity_id)
            ->firstOrFail();

        // Handle file upload if a new file is provided
        if ($request->hasFile('activity-attachments')) {
            $attachments = [];
            foreach ($request->file('activity-attachments') as $file) {
                $filename = uniqid() . '_' . $file->getClientOriginalName();
                $file->move(public_path('/attachments/leads'), $filename);
                $attachments[] = $filename;
            }
            $engagement->attachments = json_encode($attachments); // Save as a JSON array
        }

        // Update the engagement with new data
        $engagement->date = $request->input('activity-date');
        $engagement->details = $request->input('activity-details');
        $engagement->activity_name = $request->input('activity-name');
        $engagement->save();

        // Redirect to the contact view page with a success message
        return redirect()->route('contact#view', ['contact_pid' => $contact_pid])
            ->with('success', 'Activity updated successfully.');
    }
}
