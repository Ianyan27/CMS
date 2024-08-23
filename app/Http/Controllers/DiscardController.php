<?php

namespace App\Http\Controllers;

use App\Models\ContactDiscard;
use App\Models\EngagementDiscard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DiscardController extends Controller{

    public function viewDiscard($contact_discard_pid){
        $editDiscard = ContactDiscard::where('contact_discard_pid', $contact_discard_pid)->first();
        $engagementDiscard = EngagementDiscard::where('fk_engagement_discards__contact_discard_pid', $contact_discard_pid)->get();
        return view ('Edit_Discard_Detail_Page')->with([
            'editDiscard'=>$editDiscard, 
            'engagementDiscard'=>$engagementDiscard
        ]);
    }

    public function updateDiscard(Request $request, $contact_discard_pid){
         // Determine if the contact exists in the original table
        $discardContact = ContactDiscard::find($contact_discard_pid);
        $discardContact->name = $request->input('name');
        $discardContact->email = $request->input('email');
        $discardContact->contact_number = $request->input('contact_number');
        $discardContact->address = $request->input('address');
        $discardContact->country = $request->input('country');
        $discardContact->qualification = $request->input('qualification');
        $discardContact->job_role = $request->input('job_role');
        $discardContact->skills = $request->input('skills');
        $discardContact->status = $request->input('status'); // Update status as well
        $discardContact->save();
        // Redirect with a success message
        return redirect()->route('discard#view', ['contact_discard_pid' => $contact_discard_pid])->with('success', 'Contact updated successfully.');
    }

    public function saveDiscardActivity(Request $request, $contact_discard_pid){
        $validator = Validator::make($request->all(), [
            'activity-date' => 'required',
            'activity-name' => 'required',
            'activity-details' => 'required',
            'activity-attachments' => 'required|file'
        ]);

        if ($validator->fails()){
            return back()->withErrors($validator)->withInput();
        }

        $engagement = new EngagementDiscard();

        if ($request->hasFile('activity-attachments')){
            $imageFile = $request->file('activity-attachments');
            $imageName = uniqid() . '_' . $imageFile->getClientOriginalName();
            $imageFile->move(public_path('/attachments/discard'), $imageName);
            $engagement->attachments = json_encode([$imageName]); // Save as a JSON array
        }

        $engagement->date = $request->input('activity-date');
        $engagement->details = $request->input('activity-details');
        $engagement->activity_name = $request->input('activity-name');
        $engagement->fk_engagement_discards__contact_discard_pid = $request->input('discard_contact_pid');
        $engagement->save();

        return redirect()->route('discard#view', ['contact_discard_pid'=> $contact_discard_pid]);
    }
}