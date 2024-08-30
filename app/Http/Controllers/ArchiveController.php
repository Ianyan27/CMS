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

class ArchiveController extends Controller{
    public function viewArchive($id){
        $editArchive = ContactArchive::where('contact_archive_pid', $id)->first();
        $user = Auth::user();
        $engagementArchive = EngagementArchive::where('fk_engagement_archives__contact_archive_pid', $id)->get();
        return view('Edit_Archive_Detail_Page')->with([
            'editArchive'=>$editArchive, 
            'engagementArchive'=>$engagementArchive,
            'user' => $user
        ]);
    }

    public function updateArchive(Request $request, $contact_archive_pid, $id) {
        $archive = ContactArchive::find($contact_archive_pid);
        if (in_array($request->input('status'), ['InProgress', 'Discard'])) {
            $targetModel = $request->input('status') === 'InProgress' ? new Contact() : new ContactDiscard();
            $targetModel->fill($archive->toArray());
            $targetModel->status = $request->input('status');
            
            if($request->input('status') === 'InProgress'){
                $targetModel->fk_contacts__owner_pid = $id;
            } else {
                $targetModel->fk_contacts__discards_pid = $id;
            }
            $targetModel->save();
    
            // Assign the correct primary key to $newContactId
            $newContactId = $request->input('status') === 'InProgress'
                            ? $targetModel->contact_pid // Contact primary key
                            : $targetModel->contact_discard_pid; // ContactDiscard primary key
    
            $activities = EngagementArchive::where('fk_engagement_archives__contact_archive_pid', $contact_archive_pid)->get();
            $targetActivity = $request->input('status') === 'InProgress' ? new Engagement() : new EngagementDiscard();
    
            foreach($activities as $activity){
                $newActivity = $targetActivity->newInstance();
                $newActivity->fill($activity->toArray());
    
                if($request->input('status') === 'InProgress'){
                    $newActivity->fk_engagements__contact_pid = $newContactId;
                } else {
                    $newActivity->fk_engagements_discards__contact_pid = $newContactId;
                }
                $newActivity->save();
            }
    
            $archive->delete();
    
            EngagementArchive::where('fk_engagement_archives__contact_archive_pid', $contact_archive_pid);
            return redirect()->route('contact-listing')->with('success', 'Contact moved to ' . $request->input('status') . ' successfully.');
        }
    
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
    
        return redirect()->route('archive#view', ['contact_archive_pid' => $contact_archive_pid])
                        ->with('success', 'Contact updated successfully.');
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
}