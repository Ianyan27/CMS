<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\ContactArchive;
use App\Models\ContactDiscard;
use App\Models\Engagement;
use App\Models\EngagementArchive;
use App\Models\EngagementDiscard;
use App\Models\Owner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ArchiveController extends Controller
{
    public function viewArchive($contact_archive_pid){
        $editArchive = ContactArchive::where('contact_archive_pid', $contact_archive_pid)->first();
        $user = Auth::user();
        $owner = Owner::where('owner_email_id', $user->email)->first();
        $engagementArchive = EngagementArchive::where('fk_engagement_archives__contact_archive_pid', $contact_archive_pid)->get();

        // Decrypt images in engagements
        foreach ($engagementArchive as $engagement) {
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

        // Pass the entire engagement collection to the view, not just the first record.
        return view('Edit_Archive_Detail_Page')->with([
            'editArchive' => $editArchive,
            'engagementArchive' => $engagementArchive,
            'owner' => $owner,
            'updateEngagement' => $engagementArchive
        ]);
    }


    public function updateArchive(Request $request, $contact_archive_pid, $owner_pid){
        $user = Auth::user();
        $archive = ContactArchive::find($contact_archive_pid);
        $owner = Owner::where('owner_email_id', $user->email)->first();
        if (!$archive) {
            return redirect()->back()->with('error', 'Contact archive not found.');
        }

        $archive->update([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'contact_number' => $request->input('contact_number'),
            'address' => $request->input('address'),
            'country' => $request->input('country'),
            'qualification' => $request->input('qualification'),
            'job_role' => $request->input('job_role'),
            'skills' => $request->input('skills'),
            'status' => $request->input('status'),
        ]);

        if (in_array($request->input('status'), ['InProgress', 'Discard'])) {
            $targetModel = $request->input('status') === 'InProgress' ? new Contact() : new ContactDiscard();
            $targetModel->fill($archive->toArray());
            $targetModel->status = $request->input('status');

            if ($request->input('status') === 'InProgress') {
                $targetModel->fk_contacts__owner_pid = $owner_pid;
            } else {
                $targetModel->fk_contact_discards__owner_pid = $owner_pid;
            }
            $targetModel->save();

            // Assign the correct primary key to $newContactId
            $newContactId = $request->input('status') === 'InProgress'
                ? $targetModel->contact_pid
                : $targetModel->contact_discard_pid;

            $activities = EngagementArchive::where('fk_engagement_archives__contact_archive_pid', $contact_archive_pid)->get();
            $targetActivity = $request->input('status') === 'InProgress' ? new Engagement() : new EngagementDiscard();

            foreach ($activities as $activity) {
                $newActivity = $targetActivity->newInstance();
                $newActivity->fill($activity->toArray());

                if ($request->input('status') === 'InProgress') {
                    $newActivity->fk_engagements__contact_pid = $newContactId;
                } else {
                    $newActivity->fk_engagements_discards__contact_pid = $newContactId;
                }
                $newActivity->save();
            }

            // Delete the archived engagements after moving
            EngagementArchive::where('fk_engagement_archives__contact_archive_pid', $contact_archive_pid)->delete();

            // Finally, delete the archive
            $archive->delete();

            return redirect()->route('contact-listing')->with('success', 'Contact moved to ' . $request->input('status') . ' successfully.');
        }

        // Your existing logic for updating the archive
        $oldStatus = $archive->status;
        $newStatus = $request->input('status');

        if ($oldStatus !== $newStatus) {
            $this->saveLog(
                $contact_archive_pid,
                'Contact Status Updated',
                "Status changed from '$oldStatus' to '$newStatus'."
            );
        }

        return redirect()->route('archive#view', [
            'contact_archive_pid' => $contact_archive_pid
        ])->with('success', 'Contact updated successfully.');
    }


    private function saveLog($contact_archive_pid, $action_type, $action_description){

        $ownerPid = Auth::user()->id; // Get the authenticated user's ID as owner_pid

        DB::table('logs')->insert([
            'fk_logs__contact_pid' => $contact_archive_pid,
            'fk_logs__owner_pid' => $ownerPid,
            'action_type' => $action_type, // Ensure this value is one of the allowed ENUM values
            'action_description' => $action_description,
            'activity_datetime' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    public function updateActivity(Request $request, $contact_archive_pid, $activity_id)
    {
        // Validate the input data
        $validator = Validator::make($request->all(), [
            'activity-date' => 'required|date',
            'activity-name' => 'required|string',
            'activity-details' => 'required|string',
            'activity-attachments' => 'nullable|file|mimes:jpg,jpeg,png'
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
        $engagement = EngagementArchive::where('fk_engagement_archives__contact_archive_pid', $contact_archive_pid)
            ->where('engagement_archive_pid', $activity_id)
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

        // Log the update action if needed
        // $actionType = 'Activity Updated';
        // $actionDescription = "Updated activity: {$request->input('activity-name')} with details: {$request->input('activity-details')}";
        // $this->saveLog($contact_archive_pid, $actionType, $actionDescription);

        // Redirect to the contact view page with a success message
        return redirect()->route('archive#view', ['contact_archive_pid' => $contact_archive_pid])
            ->with('success', 'Activity updated successfully.');
    }


    public function saveActivity(Request $request, $contact_archive_pid)
    {
        // Validate the input data

        $validator = Validator::make($request->all(), [
            'activity-date' => 'required',
            'activity-name' => 'required',
            'activity-details' => 'required',
            'activity-attachments' => 'required|file|mimes:jpg,jpeg,png'
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
        $engagement = new EngagementArchive();

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
        $engagement->fk_engagement_archives__contact_archive_pid = $contact_archive_pid;
        $engagement->save();

        // // Save activity to the logs table
        // $actionType = 'Updated'; // Use a valid ENUM value
        // $actionDescription = "Added a new activity: {$request->input('activity-name')} with details: {$request->input('activity-details')}";

        // $this->saveLog($contact_archive_pid, $actionType, $actionDescription);

        // Redirect to the contact view page with a success message
        return redirect()->route('archive#view', ['contact_archive_pid' => $contact_archive_pid])
            ->with('success', 'Activity added successfully.');
    }
}
