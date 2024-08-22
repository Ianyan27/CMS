<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\ContactArchive;
use App\Models\ContactDiscard;
use App\Models\EngagementArchive;
use Illuminate\Http\Request;

class ArchiveController extends Controller{
    public function viewArchive($contact_archive_pid){
        $editArchive = ContactArchive::where('contact_archive_pid', $contact_archive_pid)->first();
        $engagementArchive = EngagementArchive::where('fk_engagement_archives__contact_archive_pid', $contact_archive_pid)->get();
        return view('Edit_Archive_Detail_Page')->with(['editArchive'=>$editArchive, 'engagementArchive'=>$engagementArchive]);
    }

    public function updateArchive(Request $request, $contact_archive_pid){
    $archive = ContactArchive::find($contact_archive_pid);
    if (in_array($request->input('status'), ['InProgress', 'Discard'])) {
        $targetModel = $request->input('status') === 'InProgress' ? new Contact() : new ContactDiscard();
        $targetModel->fill($archive->toArray());
        $targetModel->status = $request->input('status');
        $targetModel->save();
        $archive->delete();
        return redirect()->route('contact-listing')->with('success', 'Contact moved to ' . $request->input('status') . ' successfully.');
    }
    $archive->name = $request->input('name');
    $archive->email = $request->input('email');
    $archive->contact_number = $request->input('contact_number');
    $archive->address = $request->input('address');
    $archive->country = $request->input('country');
    $archive->qualification = $request->input('qualification');
    $archive->job_role = $request->input('job_role');
    $archive->skills = $request->input('skills');
    $archive->status = $request->input('status');
    $archive->save();

    return redirect()->route('archive#view', ['contact_archive_pid' => $contact_archive_pid])
                     ->with('success', 'Contact updated successfully.');
}

}