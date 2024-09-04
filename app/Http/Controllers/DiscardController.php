<?php

namespace App\Http\Controllers;

use App\Models\ContactDiscard;
use App\Models\EngagementDiscard;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class DiscardController extends Controller
{

    public function viewDiscard($contact_discard_pid)
    {
        $editDiscard = ContactDiscard::where('contact_discard_pid', $contact_discard_pid)->first();
        $engagementDiscard = EngagementDiscard::where('fk_engagement_discards__contact_discard_pid', $contact_discard_pid)->get();

        // Decrypt images in engagements
        foreach ($engagementDiscard as $engagement) {
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

        return view('Edit_Discard_Detail_Page')->with([
            'editDiscard' => $editDiscard,
            'engagementDiscard' => $engagementDiscard
        ]);
    }

    public function updateDiscard(Request $request, $contact_discard_pid)
    {
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

    public function saveDiscardActivity(Request $request, $contact_discard_pid)
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

        $engagement = new EngagementDiscard();

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

        $engagement->date = $request->input('activity-date');
        $engagement->details = $request->input('activity-details');
        $engagement->activity_name = $request->input('activity-name');
        $engagement->fk_engagement_discards__contact_discard_pid = $request->input('discard_contact_pid');
        $engagement->save();

        return redirect()->route('discard#view', ['contact_discard_pid' => $contact_discard_pid]);
    }
}
