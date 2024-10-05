<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\ContactArchive;
use App\Models\ContactDiscard;
use App\Models\Delete_contacts;
use App\Models\Engagement;
use App\Models\EngagementArchive;
use App\Models\EngagementDiscard;
use App\Models\Log as ModelsLog;
use App\Models\Owner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class ContactController extends Controller
{

    public function index()
    {
        // Get the logged-in user (sales agent)
        $user = Auth::user();

        // Fetch the owner record associated with the logged-in user's email
        $owner = Owner::where('owner_email_id', $user->email)->first();

        // Check if an owner was found
        if (!$owner) {
            // Handle the case where no matching owner is found
            return redirect()->back()->with('error', 'No owner found for the logged-in user.');
        }

        // Query the contacts, archive, and discard records using the owner's ID
        $contacts = Contact::where('fk_contacts__owner_pid', $owner->owner_pid)->paginate(50);
        $contactArchive = ContactArchive::where('fk_contact_archives__owner_pid', $owner->owner_pid)->paginate(50);
        $contactDiscard = ContactDiscard::where('fk_contact_discards__owner_pid', $owner->owner_pid)->paginate(50);

        // Pass the data to the view
        return view('Contact_Listing', [
            'contacts' => $contacts,
            'contactArchive' => $contactArchive,
            'contactDiscard' => $contactDiscard
        ]);
    }


    public function contactsByOwner()
    {

        $user = Auth::user();

        // Fetch the owner record associated with the logged-in user's email
        $owner = Owner::where('owner_email_id', $user->email)->first();

        // Check if an owner was found
        if (!$owner) {
            // Handle the case where no matching owner is found
            return redirect()->back()->with('error', 'No owner found for the logged-in user.');
        }

        // Query the contacts, archive, and discard records using the owner's ID
        $contacts = Contact::where('fk_contacts__owner_pid', $owner->owner_pid)->paginate(50);
        $contactArchive = ContactArchive::where('fk_contact_archives__owner_pid', $owner->owner_pid)->paginate(50);
        $contactDiscard = ContactDiscard::where('fk_contact_discards__owner_pid', $owner->owner_pid)->paginate(50);

        // Pass the data to the view
        return view('Contact_Listing', [
            'owner' => $owner,
            'contacts' => $contacts,
            'contactArchive' => $contactArchive,
            'contactDiscard' => $contactDiscard
        ]);
        // Get the logged-in user (sales agent)
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
        $owner = Owner::where('owner_email_id', $user->email)->first();

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
        $deletedEngagement = Delete_contacts::where('fk_engagements__contact_pid', $contact_pid)->get();
        // Use the first engagement for updates if available
        $updateEngagement = $engagements->first();

        // Pass data to the view
        return view('Edit_Contact_Detail_Page')->with([
            'owner' => $owner,
            'editContact' => $editContact,
            'engagements' => $engagements,
            'updateEngagement' => $updateEngagement,
            'engagementArchive' => $engagementsArchive,
            'deletedEngagement' => $deletedEngagement
        ]);
    }

    public function updateContact(Request $request, $contact_pid, $owner_pid)
    {
        // Checking for admin role and redirect if true
        $user = Auth::user();
        $owner = Owner::where('owner_email_id', $user->email)->first();
        if ($user->role === 'Admin') {
            return redirect()->route('admin#contact-listing')->with('error', 'Admin cannot edit the contact information');
        }

        // Find the contact based on the contact_pid
        $contact = Contact::find($contact_pid);

        // Check if the contact exists
        if (!$contact) {
            Log::error('Contact not found', ['contact_pid' => $contact_pid]);
            return redirect()->route('contact-listing')->with('error', 'Contact not found.');
        }

        // Fetch engagement activities associated with the contact
        $activities = Engagement::where('fk_engagements__contact_pid', $contact_pid)->get();
        $activitiesCount = $activities->count();

        // Validation rules
        $rules = [
            'status' => function ($attribute, $value, $fail) use ($activitiesCount) {
                if (in_array($value, ['InProgress', 'HubSpot Contact', 'Archive', 'Discard']) && $activitiesCount === 0) {
                    $fail('Status cannot be updated: No engagement activities for this contact.');
                }
            }
        ];

        // Validate the request with the custom rules
        $request->validate($rules);

        // Check if the owner exists
        $owner = Owner::find($owner_pid);
        if (!$owner) {
            Log::error('Owner not found', ['owner_id' => $owner_pid]);
            return redirect()->route('contact-listing')->with('error', 'Owner not found.');
        }

        // Log the owner and contact details
        Log::info('Updating contact', [
            'contact_pid' => $contact_pid,
            'owner_pid' => $owner_pid,
            'status' => $request->input('status')
        ]);

        // Update contact details if validation passes
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

        // Handle the "Archive" and "Discard" status cases after updating details
        if (in_array($request->input('status'), ['Archive', 'Discard'])) {
            $targetContactModel = $request->input('status') === 'Archive' ? new ContactArchive() : new ContactDiscard();
            $targetContactModel->fill($contact->toArray());
            $targetContactModel->status = $request->input('status');

            // Set the owner PID
            if ($request->input('status') === 'Archive') {
                $targetContactModel->fk_contact_archives__owner_pid = $owner_pid;
            } else {
                $targetContactModel->fk_contact_discards__owner_pid = $owner_pid;
            }

            Log::info('Saving contact to archive/discard', [
                'model' => $request->input('status') === 'Archive' ? 'ContactArchive' : 'ContactDiscard',
                'owner_pid' => $owner_pid
            ]);

            try {
                $targetContactModel->save();
            } catch (\Exception $e) {
                Log::error('Failed to save contact to archive/discard', [
                    'error' => $e->getMessage(),
                    'owner_pid' => $owner_pid
                ]);
                return redirect()->route('contact-listing')->with('error', 'Failed to save contact to archive/discard.');
            }

            $newContactId = $request->input('status') === 'Archive'
                ? $targetContactModel->contact_archive_pid
                : $targetContactModel->contact_discard_pid;

            $targetActivityModel = $request->input('status') === 'Archive' ? new EngagementArchive() : new EngagementDiscard();

            foreach ($activities as $activity) {
                $newActivity = $targetActivityModel->newInstance();
                $newActivity->fill($activity->toArray());

                if ($request->input('status') === 'Archive') {
                    $newActivity->fk_engagement_archives__contact_archive_pid = $newContactId;
                } else {
                    $newActivity->fk_engagement_discards__contact_discard_pid = $newContactId;
                }

                try {
                    $newActivity->save();
                } catch (\Exception $e) {
                    Log::error('Failed to save engagement activity to archive/discard', [
                        'error' => $e->getMessage(),
                        'activity_id' => $activity->id,
                        'contact_id' => $newContactId
                    ]);
                    return redirect()->route('contact-listing')->with('error', 'Failed to save engagement activities.');
                }
            }

            ModelsLog::where('fk_logs__contact_pid', $contact_pid)->delete();
            $contact->delete();
            Engagement::where('fk_engagements__contact_pid', $contact_pid)->delete();

            Log::info('Contact and activities moved successfully', [
                'contact_pid' => $contact_pid,
                'status' => $request->input('status')
            ]);

            return redirect()->route('contact-listing')->with('success', 'Contact and activities moved to ' . $request->input('status') . ' successfully.');
        }

        Log::info('Contact updated successfully', [
            'contact_pid' => $contact_pid
        ]);

        return redirect()->route('contact#view', [
            'contact_pid' => $contact_pid
        ])->with('success', 'Contact updated successfully.');
    }

    public function saveActivity(Request $request, $contact_pid)
    {
        // Checking for admin role and redirecting if true
        $user = Auth::user();

        // Validate the input data
        $validator = Validator::make($request->all(), [
            'activity-date' => 'required',
            'activity-name' => 'required',
            'activity-details' => 'required',
            'activity-attachments' => 'required|file|mimes:jpeg,png,jpg'
        ]);

        // Handle validation errors
        if ($validator->fails()) {
            if ($validator->errors()->has('activity-attachments')) {
                $attachmentErrors = $validator->errors()->get('activity-attachments');
                if (in_array('The activity attachments must be a file of type: jpeg, png, jpg.', $attachmentErrors)) {
                    return back()->withErrors(['activity-attachments' => 'Only image files (JPEG, PNG, JPG) are allowed.'])
                        ->withInput();
                }
            }
            return back()->withErrors($validator)->withInput();
        }

        // Check if the contact exists
        $contact = Contact::find($contact_pid);
        if (!$contact) {
            return back()->with('error', 'The specified contact does not exist.');
        }

        // Create a new Engagement record
        $engagement = new Engagement();

        // Handle file upload if a new file is provided
        if ($request->hasFile('activity-attachments')) {
            $imageFile = $request->file('activity-attachments');
            $imageContent = file_get_contents($imageFile);
            $encryptedImage = Crypt::encrypt($imageContent);
            $engagement->attachments = json_encode([$encryptedImage]);
        }

        // Assign engagement data from request
        $engagement->date = $request->input('activity-date');
        $engagement->details = $request->input('activity-details');
        $engagement->activity_name = $request->input('activity-name');
        $engagement->fk_engagements__contact_pid = $contact_pid;
        $engagement->save();

        // Update contact status only if the current status is 'New'
        if ($contact->status === "New") {
            $contact->status = "InProgress";
            $contact->save();
        }

        // Save activity to the logs table
        $actionType = 'Save New Activity';
        $actionDescription = "Added a new activity: {$request->input('activity-name')} with details: {$request->input('activity-details')}";

        $saveActivity = $this->saveLog($contact_pid, $actionType, $actionDescription);
        Log::info("Save Activity: " . $saveActivity);

        // Redirect to the contact view page with a success message
        return redirect()->route('contact#view', ['contact_pid' => $contact_pid])
            ->with('success', 'Activity added successfully.');
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
        // Checking for admin role and redirecting if needed
        $user = Auth::user();
        if ($user->role === 'Admin') {
            return redirect()->route('admin#contact-listing')->with('error', 'Admin cannot update the contact activity');
        }

        // Validate the input data
        $validator = Validator::make($request->all(), [
            'activity-date' => 'required|date',
            'activity-name' => 'required|string',
            'activity-details' => 'required|string',
            'activity-attachments' => 'nullable|file|mimes:jpeg,png,jpg'
        ]);

        // Handle validation errors
        if ($validator->fails()) {
            // Check if the error is related to the file type
            if ($validator->errors()->has('activity-attachments')) {
                $attachmentErrors = $validator->errors()->get('activity-attachments');
                if (in_array('The activity attachments must be a file of type: jpeg, png, jpg.', $attachmentErrors)) {
                    // Specific error message for invalid file type
                    return back()->withErrors(['activity-attachments' => 'Only image files (JPEG, PNG, JPG) are allowed.'])
                        ->withInput();
                }
            }

            // Return back with general validation errors
            return back()->withErrors($validator)->withInput();
        }

        // Retrieve the engagement record to be updated
        $engagement = Engagement::where('fk_engagements__contact_pid', $contact_pid)
            ->where('engagement_pid', $activity_id)
            ->firstOrFail();

        // Handle file upload if a new file is provided
        if ($request->hasFile('activity-attachments')) {
            $file = $request->file('activity-attachments');

            // Read the file content
            $fileContent = file_get_contents($file->getRealPath());

            // Encrypt the file content
            $encryptedContent = Crypt::encrypt($fileContent);

            // Store the encrypted content
            $engagement->attachments = json_encode([$encryptedContent]);
        }

        // Update the engagement with new data
        $engagement->date = $request->input('activity-date');
        $engagement->details = $request->input('activity-details');
        $engagement->activity_name = $request->input('activity-name');
        $engagement->save();

        // Log the update action
        $actionType = 'Activity Updated';
        $actionDescription = "Updated activity: {$request->input('activity-name')} with details: {$request->input('activity-details')}";

        $editActivity = $this->saveLog($contact_pid, $actionType, $actionDescription);

        Log::info("Edit Activity: " . $editActivity);

        // Redirect to the contact view page with a success message
        return redirect()->route('contact#view', ['contact_pid' => $contact_pid])
            ->with('success', 'Activity updated successfully.');
    }

    public function archiveActivity($engagement_archive_pid)
    {
        // Find the engagement activity by its ID (engagement_pid)
        $engagement = EngagementArchive::find($engagement_archive_pid);

        if (!$engagement) {
            return redirect()->back()->with('error', 'Activity not found.');
        }

        try {
            // Move the activity to the "deleted" table
            Delete_contacts::create([
                'fk_engagements__contact_pid' => $engagement->fk_engagement_archives__contact_archive_pid,
                'activity_name' => $engagement->activity_name,
                'date' => $engagement->date,
                'details' => $engagement->details,
                'attachments' => $engagement->attachments,
            ]);

            // Delete the activity from the "engagements" table
            $engagement->delete();
            // Log the deletion action
            Log::info('Activity moved to deleted table and removed from engagements table', [
                'engagement_pid' => $engagement_archive_pid,
                'contact_pid' => $engagement->fk_engagements__contact_pid,
            ]);
            return redirect()->back()->with('success', 'Activity deleted and moved to the deleted table successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete activity', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'An error occurred while deleting the activity.');
        }
    }

    public function archiveActivities($engagement_pid)
    {
        // Find the engagement activity by its ID (engagement_pid)
        $engagement = Engagement::find($engagement_pid);

        if (!$engagement) {
            return redirect()->back()->with('error', 'Activity not found.');
        }
        try {
            // Move the activity to the "deleted" table
            Delete_contacts::create([
                'fk_engagements__contact_pid' => $engagement->fk_engagements__contact_pid,
                'activity_name' => $engagement->activity_name,
                'date' => $engagement->date,
                'details' => $engagement->details,
                'attachments' => $engagement->attachments,
            ]);
            // Delete the activity from the "engagements" table
            $engagement->delete();
            // Log the deletion action
            Log::info('Activity moved to deleted table and removed from engagements table', [
                'engagement_pid' => $engagement_pid,
                'contact_pid' => $engagement->fk_engagements__contact_pid,
            ]);
            return redirect()->back()->with('success', 'Activity deleted and moved to the deleted table successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete activity', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'An error occurred while deleting the activity.');
        }
    }

    public function deleteActivity($fk_engagements__contact_pid){
        try {
            // Find the engagement by its engagement_pid
            $engagement = Delete_contacts::where( 'fk_engagements__contact_pid', $fk_engagements__contact_pid);

            // Permanently delete the engagement
            $engagement->delete();

            // Redirect with a success message
            return redirect()->back()->with('success', 'Activity deleted permanently.');
        } catch (\Exception $e) {
            // Handle the exception and return an error message
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete activity: ' . $e->getMessage());
        }
    }

    public function deleteArchivedActivity($fk_engagements__contact_pid){
        try {
            // Find the engagement by its engagement_pid
            $engagement = Delete_contacts::where( 'fk_engagements__contact_pid', $fk_engagements__contact_pid);

            // Permanently delete the engagement
            $engagement->delete();

            // Redirect with a success message
            return redirect()->back()->with('success', 'Activity deleted permanently.');
        } catch (\Exception $e) {
            // Handle the exception and return an error message
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete activity: ' . $e->getMessage());
        }
    }

    public function deleteArchiveActivity($engagement_archive_pid){
        try {
            // Find the engagement by its engagement_pid
            $engagement = Delete_contacts::where( 'fk_engagements__contact_pid', $engagement_archive_pid);

            // Permanently delete the engagement
            $engagement->delete();

            // Redirect with a success message
            return redirect()->back()->with('success', 'Activity deleted permanently.');
        } catch (\Exception $e) {
            // Handle the exception and return an error message
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete activity: ' . $e->getMessage());
        }
    }

    public function retrieveActivity($id)
    {
        // Retrieve the deleted activities based on engagement_pid
        $deletedContacts = Delete_contacts::where('id', $id)->get();

        if ($deletedContacts->isEmpty()) {
            return redirect()->back()->with('error', 'No activities found in archived contacts.');
        }

        try {
            foreach ($deletedContacts as $deletedContact) {
                // Move the activity back to the "engagements" table
                Engagement::create([
                    'fk_engagements__contact_pid' => $deletedContact->fk_engagements__contact_pid,
                    'activity_name' => $deletedContact->activity_name,
                    'date' => $deletedContact->date,
                    'details' => $deletedContact->details,
                    'attachments' => $deletedContact->attachments,
                ]);

                // Delete the activity from the deleted contacts table
                $deletedContact->delete();

                // Log the restoration action
                Log::info('Activity restored from deleted contacts and moved back to engagements', [
                    'fk_engagements__contact_pid' => $deletedContact->fk_engagements__contact_pid,
                ]);
            }

            return redirect()->back()->with('success', 'Activities restored successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to restore activities', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);
            return redirect()->back()->with('error', 'An error occurred while restoring the activities.');
        }
    }



    public function hubspotContacts()
    {
        $ownerPid = Auth::user()->id; // Get the authenticated user's ID as owner_pid

        log::info("buh id " . $ownerPid);

        // Get HubSpot contacts using Eloquent with joins and pagination
        $hubspotContacts = Contact::join('owners as o', 'contacts.fk_contacts__owner_pid', '=', 'o.owner_pid')
            ->join('users as u', 'o.fk_buh', '=', 'u.id')
            ->where('u.id', $ownerPid)
            ->where('contacts.status', 'Hubspot Contact')
            ->select('contacts.*') // Adjust as necessary
            ->paginate(50);

        // Get HubSpot contacts where datetime_of_hubspot_sync is null using the same query structure
        $hubspotContactsNoSync = Contact::join('owners as o', 'contacts.fk_contacts__owner_pid', '=', 'o.owner_pid')
            ->join('users as u', 'o.fk_buh', '=', 'u.id')
            ->where('u.id', $ownerPid)
            ->where('contacts.status', 'Hubspot Contact')
            ->whereNull('contacts.datetime_of_hubspot_sync')
            ->select('contacts.*') // Adjust as necessary
            ->paginate(50);

        // Get HubSpot contacts where datetime_of_hubspot_sync has a value using the same query structure
        $hubspotContactsSynced = Contact::join('owners as o', 'contacts.fk_contacts__owner_pid', '=', 'o.owner_pid')
            ->join('users as u', 'o.fk_buh', '=', 'u.id')
            ->where('u.id', $ownerPid)
            ->where('contacts.status', 'Hubspot Contact')
            ->whereNotNull('contacts.datetime_of_hubspot_sync')
            ->select('contacts.*') // Adjust as necessary
            ->paginate(50);

        // Pass data to view
        return view('Hubspot_Contact_Listing', [
            'hubspotContacts' => $hubspotContacts,
            'hubspotContactsNoSync' => $hubspotContactsNoSync,
            'hubspotContactsSynced' => $hubspotContactsSynced
        ]);
    }

    private function saveLog($contact_pid, $action_type, $action_description)
    {
        $ownerEmail = Auth::user()->email; // Get the authenticated user's ID as owner_pid

        // Check if the owner exists in the owners table
        $ownerExists = DB::table('owners')->where('owner_email_id', $ownerEmail)->exists();
        $ownerDetails = DB::table('owners')->where('owner_email_id', $ownerEmail)->first();
        if (!$ownerExists) {
            // Handle the case where the owner is not found
            Log::error("Invalid Email {$ownerEmail}");
            return false;
        }

        // Insert the log record
        DB::table('logs')->insert([
            'fk_logs__contact_pid' => $contact_pid,
            'fk_logs__owner_pid' => $ownerDetails->owner_pid,
            'action_type' => $action_type, // Ensure this value is one of the allowed ENUM values
            'action_description' => $action_description,
            'activity_datetime' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return true;
    }
}
