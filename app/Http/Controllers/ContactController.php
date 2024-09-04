<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\ContactArchive;
use App\Models\ContactDiscard;
use App\Models\Engagement;
use App\Models\EngagementArchive;
use App\Models\EngagementDiscard;
use App\Models\Log as ModelsLog;
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

        // Get contacts related to this sales agent
        $contacts = Contact::where('fk_contacts__owner_pid', $user->id)->paginate(50);
        $contactArchive = ContactArchive::where('fk_contact_archives__owner_pid', $user->id)->paginate(50);
        $contactDiscard = ContactDiscard::where('fk_contact_discards__owner_pid', $user->id)->paginate(50);

        // Pass the data to the view
        return view('Contact_Listing', [
            'contacts' => $contacts,
            'contactArchive' => $contactArchive,
            'contactDiscard' => $contactDiscard
        ]);
    }

    public function contactsByOwner()
    {
        // Get the logged-in user (sales agent)
        $user = Auth::user();

        // Get contacts related to this sales agent
        $contacts = Contact::where('fk_contacts__owner_pid', $user->id)->paginate(50);
        $contactArchive = ContactArchive::where('fk_contact_archives__owner_pid', $user->id)->paginate(50);
        $contactDiscard = ContactDiscard::where('fk_contact_discards__owner_pid', $user->id)->paginate(50);

        // Pass the data to the view
        return view('Contact_Listing', [
            'contacts' => $contacts,
            'contactArchive' => $contactArchive,
            'contactDiscard' => $contactDiscard
        ]);
    }

    public function viewContact($contact_pid)
    {
        /* Retrieve the contact record with the specified 'contact_pid' and pass
         it to the 'Edit_Contact_Detail_Page' view for editing. */

        // Retrieve the contact record with the specified 'contact_pid'
        $editContact = Contact::where('contact_pid', $contact_pid)->first();

        // Check if the contact exists
        if (!$editContact) {
            return redirect()->route('contacts.list')->with('error', 'Contact not found.');
        }

        // Retrieve the authenticated user
        $user = Auth::user();

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

        // Use the first engagement for updates if available
        $updateEngagement = $engagements->first();

        // Pass data to the view
        return view('Edit_Contact_Detail_Page')->with([
            'user' => $user,
            'editContact' => $editContact,
            'engagements' => $engagements,
            'updateEngagement' => $updateEngagement,
            'engagementArchive' => $engagementsArchive
        ]);
    }

    public function updateContact(Request $request, $contact_pid, $id){
        // Checking for admin role and redirect if true
        $user = Auth::user();
        if ($user->role === 'Admin') {
            return redirect()->route('admin#contact-listing')->with('error', 'Admin cannot edit the contact information');
        }

        // Find the contact based on the contact_pid
        $contact = Contact::find($contact_pid);

        // Check if the contact exists
        if (!$contact) {
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
            'status' => $request->input('status') // Update the status here as well
        ]);

        // Handle the "Archive" and "Discard" status cases after updating details
        if (in_array($request->input('status'), ['Archive', 'Discard'])) {
            // Determine the target model based on the status
            $targetContactModel = $request->input('status') === 'Archive' ? new ContactArchive() : new ContactDiscard();

            // Copy the contact data to the new table
            $targetContactModel->fill($contact->toArray());
            $targetContactModel->status = $request->input('status'); // Explicitly set the status

            // Set the owner PID based on the user who updated the contact
            if ($request->input('status') === 'Archive') {
                $targetContactModel->fk_contact_archives__owner_pid = $id;
            } else {
                $targetContactModel->fk_contact_discards__owner_pid = $id;
            }

            $targetContactModel->save();

            // Get the new contact ID from the archive or discard table
            $newContactId = $request->input('status') === 'Archive'
                ? $targetContactModel->contact_archive_pid
                : $targetContactModel->contact_discard_pid;

            // Move related activities
            $targetActivityModel = $request->input('status') === 'Archive' ? new EngagementArchive() : new EngagementDiscard();

            foreach ($activities as $activity) {
                $newActivity = $targetActivityModel->newInstance(); // Create a new instance for each activity
                $newActivity->fill($activity->toArray());

                // Set the foreign key to reference the newly created contact
                if ($request->input('status') === 'Archive') {
                    $newActivity->fk_engagement_archives__contact_archive_pid = $newContactId;
                } else {
                    $newActivity->fk_engagement_discards__contact_discard_pid = $newContactId;
                }

                $newActivity->save();
            }

            // Delete related logs
            ModelsLog::where('fk_logs__contact_pid', $contact_pid)->delete();

            // Delete the contact from the current table
            $contact->delete();

            // Delete the engagement activities from the current table
            Engagement::where('fk_engagements__contact_pid', $contact_pid)->delete();

            // Redirect with a success message
            return redirect()->route('contact-listing')->with('success', 'Contact and activities moved to ' . $request->input('status') . ' successfully.');
        }

        // If status is not "Archive" or "Discard", just redirect after updating
        return redirect()->route('contact#view', [
            'contact_pid' => $contact_pid
        ])->with('success', 'Contact updated successfully.');
    }

    public function saveActivity(Request $request, $contact_pid){

        //checking for admin role and redirect it
        $user = Auth::user();
        if ($user->role === 'Admin') {
            return redirect()->route('admin#contact-listing')->with('error', 'Admin cannot save the contact activity');
        }

        // Validate the input data
        $validator = Validator::make($request->all(), [
            'activity-date' => 'required',
            'activity-name' => 'required',
            'activity-details' => 'required',
            'activity-attachments' => 'required|file|mimes:jpeg,png,jpg'
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

        // Create a new Engagement record
        $engagement = new Engagement();

        // Handle file upload if a new file is provided
        if ($request->hasFile('activity-attachments')) {
            $imageFile = $request->file('activity-attachments');
            $imageContent = file_get_contents($imageFile);
            $encryptedImage = Crypt::encrypt($imageContent); // Encrypt the image content
            // Encrypt the image content
            $encryptedImage = Crypt::encrypt($imageContent);

            // Save as a JSON array
            $engagement->attachments = json_encode([$encryptedImage]);
        }

        // Assign engagement data from request
        $engagement->date = $request->input('activity-date');
        $engagement->details = $request->input('activity-details');
        $engagement->activity_name = $request->input('activity-name');
        $engagement->fk_engagements__contact_pid = $contact_pid;
        $engagement->save();

        // Update contact status
        $contact = Contact::find($contact_pid);
        if ($contact) {
            $contact->status = "InProgress";
            $contact->save();
        }

        // Save activity to the logs table
        $actionType = 'Save New Activity'; // Use a valid ENUM value
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
            // If the validation fails, return the errors
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

        $ownerPid = Auth::user()->id; // Get the authenticated user's ID as owner_pid

        DB::table('logs')->insert([
            'fk_logs__contact_pid' => $contact_pid,
            'fk_logs__owner_pid' => $ownerPid,
            'action_type' => $action_type, // Ensure this value is one of the allowed ENUM values
            'action_description' => $action_description,
            'activity_datetime' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
