<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\ContactArchive;
use App\Models\ContactDiscard;
use App\Models\Engagement;
use App\Models\EngagementArchive;
use App\Models\EngagementDiscard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ArchiveController extends Controller{
    public function viewArchive($id) {
        $editArchive = ContactArchive::where('contact_archive_pid', $id)->first();
        $user = Auth::user();
        $engagementArchive = EngagementArchive::where('fk_engagement_archives__contact_archive_pid', $id)->get();
        
        // Pass the entire engagement collection to the view, not just the first record.
        return view('Edit_Archive_Detail_Page')->with([
            'editArchive' => $editArchive, 
            'engagementArchive' => $engagementArchive,
            'user' => $user,
            'updateEngagement' => $engagementArchive
        ]);
    }
    

    public function updateArchive(Request $request, $contact_archive_pid, $id) {
        $archive = ContactArchive::find($contact_archive_pid);
    
        if (!$archive) {
            return redirect()->back()->with('error', 'Contact archive not found.');
        }
    
        if (in_array($request->input('status'), ['InProgress', 'Discard'])) {
            $targetModel = $request->input('status') === 'InProgress' ? new Contact() : new ContactDiscard();
            $targetModel->fill($archive->toArray());
            $targetModel->status = $request->input('status');
            
            if ($request->input('status') === 'InProgress') {
                $targetModel->fk_contacts__owner_pid = $id;
            } else {
                $targetModel->fk_contacts__discards_pid = $id;
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

    public function updateActivity(Request $request, $contact_archive_pid, $activity_id){
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
        $engagement = EngagementArchive::where('fk_engagement_archives__contact_archive_pid', $contact_archive_pid)
            ->where('engagement_archive_pid', $activity_id)
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

        // // Log the update action
        // $actionType = 'Activity Updated'; // Example action type
        // $actionDescription = "Updated activity: {$request->input('activity-name')} with details: {$request->input('activity-details')}"; // Example action description

        // $this->saveLog($contact_pid, $actionType, $actionDescription);

        // Redirect to the contact view page with a success message
        return redirect()->route('archive#view', ['contact_archive_pid' => $contact_archive_pid])
            ->with('success', 'Activity updated successfully.');
    }

    public function saveActivity(Request $request, $contact_archive_pid){
        // Validate the input data
        
        $validator = Validator::make($request->all(), [
            'activity-date' => 'required',
            'activity-name' => 'required',
            'activity-details' => 'required',
            'activity-attachments' => 'required|file'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Create a new Engagement record
        $engagement = new EngagementArchive();

        // Handle file upload if a new file is provided
        if ($request->hasFile('activity-attachments')) {
            $imageFile = $request->file('activity-attachments');
            $imageName = uniqid() . '_' . $imageFile->getClientOriginalName();
            $imageFile->move(public_path('/attachments/leads'), $imageName);
            $engagement->attachments = json_encode([$imageName]); // Save as a JSON array
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