<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\ContactArchive;
use App\Models\ContactDiscard;
use App\Models\Engagement;
use App\Models\EngagementArchive;
use App\Models\EngagementDiscard;
use App\Models\SaleAgent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DiscardController extends Controller
{

    public function viewDiscard($contact_discard_pid)
    {
        $editDiscard = ContactDiscard::where('contact_discard_pid', $contact_discard_pid)->first();
        $user = Auth::user();
        $owner = SaleAgent::where('email', $user->email)->first();
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
            'owner' => $owner,
            'engagementDiscard' => $engagementDiscard
        ]);
    }

    public function updateDiscard(Request $request, $contact_discard_pid, $id)
    {
        // Determine if the contact exists in the original table
        $discardContact = ContactDiscard::find($contact_discard_pid);

        $discardContact->update([
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
        
        if (in_array($request->input('status'), ['InProgress', 'Archive'])) {
            $targetModel = $request->input('status') === 'InProgress' ? new Contact() : new ContactArchive();
            $targetModel->fill($discardContact->toArray());
            $targetModel->status = $request->input('status');

            if ($request->input('status') === 'InProgress') {
                $targetModel->fk_contacts__sale_agent_id = $id;
            } else {
                $targetModel->fk_contacts__sale_agent_id = $id;
            }
            $targetModel->save();

            // Assign the correct primary key to $newContactId
            $newContactId = $request->input('status') === 'InProgress'
                ? $targetModel->contact_pid
                : $targetModel->contact_archive_pid;

            $activities = EngagementDiscard::where('fk_engagement_discards__contact_discard_pid', $contact_discard_pid)->get();
            $targetActivity = $request->input('status') === 'InProgress' ? new Engagement() : new EngagementArchive();

            foreach ($activities as $activity) {
                $newActivity = $targetActivity->newInstance();
                $newActivity->fill($activity->toArray());

                if ($request->input('status') === 'InProgress') {
                    $newActivity->fk_engagements__contact_pid = $newContactId;
                } else {
                    $newActivity->fk_engagement_archives__contact_archive_pid = $newContactId;
                }
                $newActivity->save();
            }

            // Delete logs related to this contact archive before deleting the archive itself
            DB::table('archive__logs')->where('fk_logs__archive_contact_pid', $contact_discard_pid)->delete();

            // Now delete the discard engagements after moving
            EngagementDiscard::where('fk_engagement_discards__contact_discard_pid', $contact_discard_pid)->delete();

            // Finally, delete the discard
            $discardContact->delete();


            return redirect()->route('sale-agent#contact-listing')->with('success', 'Contact moved to ' . $request->input('status') . ' successfully.');
        }

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
