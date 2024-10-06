<?php

namespace App\Http\Controllers;

use App\Models\ArchiveActivities;
use App\Models\BU;
use App\Models\Contact;
use App\Models\ContactArchive;
use App\Models\ContactDiscard;
use App\Models\Delete_contacts;
use App\Models\Engagement;
use App\Models\EngagementArchive;
use App\Models\EngagementDiscard;
use App\Models\Log as ModelsLog;
use App\Models\MovedContact;
use App\Models\Owner;
use App\Models\SaleAgent;
use App\Models\User;
use App\Services\RoundRobinAllocator;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller{

    public function index()
    {
        $userData = User::paginate(10);
        return view('User_List_Page', [
            'userData' => $userData
        ]);
    }

    public function viewUser()
    {
        $userData = User::paginate(10);
        return view('User_List_Page', ['userData' => $userData]);
    }

    public function contacts(){
        $user = Auth::user();
        $contacts = Contact::paginate(10);
        $contactArchive = ContactArchive::paginate(10);
        $contactDiscard = ContactDiscard::paginate(10);
        return view('Contact_Listing', [
            'user' => $user,
            'contacts' => $contacts,
            'contactArchive' => $contactArchive,
            'contactDiscard' => $contactDiscard
        ]);
    }

    public function saveUser(Request $request){

        // Define the allowed email domains
        $allowedDomains = ['lithan.com', 'educlaas.com', 'learning.educlaas.com'];
        $domainRegex = implode('|', array_map(function ($domain) {
            return preg_quote($domain, '/');
        }, $allowedDomains));

        // Validate the request
        $request->validate([
            'name' => 'required|string|min:3|max:50',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users',
                function ($attribute, $value, $fail) use ($domainRegex) {
                    if (!preg_match('/@(' . $domainRegex . ')$/', $value)) {
                        $fail('The email address must be one of the following domains: ' . str_replace('|', ', ', $domainRegex));
                    }
                }
            ],
            'password' => 'required|string|min:8|confirmed',
        ]);

        // hard code role
        $role = " ";
        // Create a new user
        User::create([
            'role' => null,
            'name' => $request->name,
            'email' => $request->email,
            'role' => $role,
            'password' => bcrypt($request->password), // Encrypt the password
        ]);
        return redirect()->back()->with('success', 'User created successfully');
    }

    public function editUser($id){
        $editUser = User::find($id);
        return view('Edit_User_Detail_Page', ['editUser' => $editUser]);
    }

    public function updateUser(Request $request, $id)
    {
        $updateUser = User::find($id);
        $updateUser->update([
            $updateUser->name = $request->input('name'),
            $updateUser->email = $request->input('email'),
            $updateUser->role = $request->input('role'),
        ]);

        return redirect()->route('admin#index')->with('success', 'User updated successfully');
    }

    public function deleteUser($id)
    {
        User::where('id', $id)->delete();
        return redirect()->route('admin#index')->with('success', 'User Deleted Successfully');
    }

    public function viewContact($contact_pid){
        /* Retrieve the contact record with the specified 'contact_pid' and pass
         it to the 'Edit_Contact_Detail_Page' view for editing. */

        // Retrieve the contact record with the specified 'contact_pid'
        $editContact = Contact::where('contact_pid', $contact_pid)->first();

        // Check if the contact exists
        if (!$editContact) {
            return redirect()->route('admin#contact-listing')->with('error', 'Contact not found.');
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
        $deletedEngagement = ArchiveActivities::where('fk_engagements__contact_pid', $contact_pid)->get();
        // Use the first engagement for updates if available
        $updateEngagement = $engagements->first();

        // Pass data to the view
        return view('Edit_Contact_Detail_Page')->with([
            'user' => $user,
            'owner' => $owner,
            'editContact' => $editContact,
            'engagements' => $engagements,
            'updateEngagement' => $updateEngagement,
            'engagementArchive' => $engagementsArchive,
            'deletedEngagement' => $deletedEngagement
        ]);
    }

    public function saleAdmin()
    {
        $businessUnit = BU::all();
        $owners = SaleAgent::all();
        return view('Sale_Admin_Page')->with([
            'owners' => $owners,
            'businessUnit' => $businessUnit
        ]);
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

    public function saleAgent(){
        // Get the current authenticated user
        $user = Auth::user();
        // Check if the user is a BUH or Admin
        if ($user->role == 'BUH') {
            // If the user is BUH, filter owners by the BUH's fk_buh
            $owner = Owner::where('fk_buh', $user->id)->paginate(10);
            $contact = Contact::where('fk_contacts__owner_pid', null)->count();
            // $archiveContact = ContactArchive::where('fk_contact_archives__owner_pid', null)->count();
            // $discardContact = ContactDiscard::where('fk_contact_discards__owner_pid', null)->count();
            Log::info("Total of unassigned contacts: " . $contact);
        } else {
            // If the user is Admin, show all owners
            $owner = Owner::paginate(10);
            $contact = Contact::get();
        }

        // Get Hubspot sales agents
        $hubspotSalesAgents = $this->getHubspotSalesAgents();

        // Return the view with the appropriate data
        return view('Sale_Agent_Page', [
            'owner' => $owner,
            'user' => $user,
            'hubspotSalesAgents' => $hubspotSalesAgents,
            'contact' => $contact
        ]);
    }

    protected function getHubspotSalesAgents(){
        try {
            $client = new Client();
            $response = $client->request('GET', 'https://api.hubapi.com/crm/v3/owners', [
                'headers' => [
                    'Authorization' => 'Bearer ' . env('HUBSPOT_API_KEY'),
                    'Content-Type'  => 'application/json',  
                ],
                'verify' => false
            ]);

            $hubspotSalesAgents = json_decode($response->getBody(), true);

            // Paginate the Hubspot sales agents
            $hubspotSalesAgents = collect($hubspotSalesAgents);

            return $hubspotSalesAgents;
        } catch (\Exception $e) {
            // Handle any errors that occur during the request
            Log::error($e->getMessage());
            return [];
        }
    }

    public function viewSaleAgent($owner_pid){
        $owner = Owner::where('owner_pid', $owner_pid)->first();
        // Execute the queries to get the actual data
        $editOwner = Owner::where('owner_pid', $owner_pid)->first();

        // Get the total contacts count allocated to this owner
        $totalContacts = Contact::where('fk_contacts__owner_pid', $owner_pid)->count();
        $totalArchive = ContactArchive::where('fk_contact_archives__owner_pid', $owner_pid)->count();
        $totalDiscard = ContactDiscard::where('fk_contact_discards__owner_pid', $owner_pid)->count();

        $totalContact = $totalContacts + $totalArchive + $totalDiscard;
        // Get the count of contacts where status is 'HubSpot'
        $hubspotContactsCount = Contact::where('fk_contacts__owner_pid', $owner_pid)
            ->where('status', 'HubSpot Contact')
            ->count();

        // Get the count of current engaging contact
        $hubspotCurrentEngagingContact = Contact::where('fk_contacts__owner_pid', $owner_pid)
            ->where('status', 'InProgress')
            ->count();

        // Update the 'total_hubspot_sync' column in the 'owners' table
        $editOwner->total_assign_contacts = $totalContact;
        $editOwner->total_hubspot_sync = $hubspotContactsCount;
        $editOwner->total_in_progress = $hubspotCurrentEngagingContact;
        $editOwner->total_archive_contacts = $totalArchive;
        $editOwner->total_discard_contacts = $totalDiscard;
        $editOwner->save();

        // Get the contacts
        $ownerContacts = Contact::where(
            'fk_contacts__owner_pid',
            $owner_pid
        )->paginate(50);

        $ownerArchive = ContactArchive::where(
            'fk_contact_archives__owner_pid',
            $owner_pid
        )->paginate(50);
        $ownerDiscard = ContactDiscard::where(
            'fk_contact_discards__owner_pid',
            $owner_pid
        )->paginate(50);

        // Pass the data to the view
        return view('Edit_Owner_Detail_Page', [
            'editOwner' => $editOwner,
            'totalContacts' => $totalContacts,
            'hubspotContactsCount' => $hubspotContactsCount,
            'hubspotCurrentEngagingContact' => $hubspotCurrentEngagingContact,
            'ownerContacts' => $ownerContacts,
            'ownerArchive' => $ownerArchive,
            'ownerDiscard' => $ownerDiscard,
            'owner' => $owner
        ]);
    }

    public function updateSaleAgent(Request $request, $owner_pid){

        $owner = Owner::find($owner_pid);

        $owner->update([
            $owner->owner_business_unit = $request->input('business_unit'),
            $owner->country = $request->input('country')
        ]);

        return redirect()->route('owner#view-owner', [
            'owner_pid' => $owner_pid
        ])->with('success', 'Sale Agent updated successfully.');
    }

    public function updateContact(Request $request, $contact_pid, $id){
        // Checking for admin role and redirect if true
        $user = Auth::user();
        $owner = Owner::where('owner_email_id', $user->email)->first();

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

        // Log the owner and contact details
        Log::info('Updating contact', [
            'contact_pid' => $contact_pid,
            'id' => $id,
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
                $targetContactModel->fk_contact_archives__owner_pid = $id;
            } else {
                $targetContactModel->fk_contact_discards__owner_pid = $id;
            }

            Log::info('Saving contact to archive/discard', [
                'model' => $request->input('status') === 'Archive' ? 'ContactArchive' : 'ContactDiscard',
                'owner_pid' => $id
            ]);

            try {
                $targetContactModel->save();
            } catch (\Exception $e) {
                Log::error('Failed to save contact to archive/discard', [
                    'error' => $e->getMessage(),
                    'owner_pid' => $id
                ]);
                return redirect()->route('admin#contact-listing')->with('error', 'Failed to save contact to archive/discard.');
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
                    return redirect()->route('admin#contact-listing')->with('error', 'Failed to save engagement activities.');
                }
            }
            ModelsLog::where('fk_logs__contact_pid', $contact_pid)->delete();
            $contact->delete();
            Engagement::where('fk_engagements__contact_pid', $contact_pid)->delete();

            Log::info('Contact and activities moved successfully', [
                'contact_pid' => $contact_pid,
                'status' => $request->input('status')
            ]);

            return redirect()->route('admin#contact-listing')->with('success', 'Contact and activities moved to ' . $request->input('status') . ' successfully.');
        }

        Log::info('Contact updated successfully', [
            'contact_pid' => $contact_pid
        ]);

        return redirect()->route('admin#view-contact', [
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
        return redirect()->route('admin#view-contact', ['contact_pid' => $contact_pid])
            ->with('success', 'Activity added successfully.');
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

    public function importCSV(){
        return view('csv_import_form');
    }

    public function viewBUH()
    {
        $userData = DB::table('bu_country as bc')
            ->join('bu', 'bc.bu_id', '=', 'bu.id')
            ->join('country', 'bc.country_id', '=', 'country.id')
            ->join('buh', 'bc.buh_id', '=', 'buh.id')
            ->select(
                'bc.id as id',
                'bu.name as bu_name',
                'country.name as country_name',
                'buh.name as buh_name',
                'buh.email as buh_email',
                'buh.nationality' // Include nationality here
            )
            ->paginate(10);

        // Pass the current page and per page values to the view
        $currentPage = $userData->currentPage();
        $perPage = $userData->perPage();

        // Pass the results to the view
        return view('Head_page', [
            'userData' => $userData,
            'currentPage' => $currentPage,
            'perPage' => $perPage,
            'countries' => DB::table('country')->get(),
            'businessUnits' => DB::table('bu')->get()
        ]);
    }
}
