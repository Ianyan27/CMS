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

class AdminController extends Controller
{

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

    public function contacts()
    {
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

    public function saveUser(Request $request)
    {

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

    public function editUser($id)
    {
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

    public function viewTransferableContact($contact_pid, $type)
    {
        /* Retrieve the contact record with the specified 'contact_pid' and pass
         it to the 'Edit_Contact_Detail_Page' view for editing. */

        // Retrieve the contact record with the specified 'contact_pid'
        $contact = null;

    // Check which type of contact it is and retrieve it from the corresponding table
        switch ($type) {
            case 'New':
            case 'HubSpot Contact':
            case 'InProgress':
                $contact = Contact::where('contact_pid', $contact_pid)->first();
                break;
            case 'archive':
                $contact = ContactArchive::where('contact_archive_pid', $contact_pid)->first();
                break;
            case 'discard':
                $contact = ContactDiscard::where('contact_discard_pid', $contact_pid)->first();
                break;
            default:
                abort(404, 'Contact not found');
        }

        // Check if the contact exists
        if (!$contact) {
            return redirect()->route('admin#view-sale-agent')->with('error', 'Contact not found.');
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
            'editContact' => $contact,
            'engagements' => $engagements,
            'updateEngagement' => $updateEngagement,
            'engagementArchive' => $engagementsArchive,
            'deletedEngagement' => $deletedEngagement
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
            return redirect()->route('admin#view-contact', $contact_pid)->with('error', 'Contact not found.');
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

    public function saleAgent()
    {
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

    protected function getHubspotSalesAgents()
    {
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

    public function viewSaleAgent($owner_pid)
    {
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

    public function updateSaleAgent(Request $request, $owner_pid)
    {

        $owner = Owner::find($owner_pid);

        $owner->update([
            $owner->owner_business_unit = $request->input('business_unit'),
            $owner->country = $request->input('country')
        ]);

        return redirect()->route('owner#view-owner', [
            'owner_pid' => $owner_pid
        ])->with('success', 'Sale Agent updated successfully.');
    }

    public function updateContact(Request $request, $contact_pid, $id)
    {
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

    public function importCSV()
    {
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

        // // Pass the current page and per page values to the view
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

    public function transferContact($id)
    {
        Session::put('progress', 0);
        $user = Auth::user();

        $contacts = Contact::get();
        $archivedContacts = ContactArchive::get();
        $discardedContacts = ContactDiscard::get();

        $owner = SaleAgent::where('id', $id)->first();
        $allContacts = $contacts->concat($archivedContacts)->concat($discardedContacts);
        $countAllContacts = $allContacts->count();
        $countEligibleContacts = $contacts->concat($archivedContacts);
        $totalEligibleContacts = $countEligibleContacts->count();
        $perPage = 50;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentPageItems = $allContacts->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $paginatedContacts = new LengthAwarePaginator($currentPageItems, $allContacts->count(), $perPage);
        $paginatedContacts->setPath(request()->url());

        // Determine if the combined collection is empty
        $isEmpty = $allContacts->isEmpty();
        return view('Transfer_Contacts_Page', [
            'owner' => $owner,
            'viewContact' => $paginatedContacts,
            'isEmpty' => $isEmpty,
            'countAllContacts' => $countAllContacts,
            'totalEligibleContacts' => $totalEligibleContacts
        ]);
    }

    public function saveBUH(Request $request){
        // Define the allowed email domains as a regular expression
        $domainRegex = 'lithan.com|educlaas.com|learning.educlaas.com';

        // Validate the request data
        $validatedData = $request->validate([
            'name' => 'required|string|min:3|max:50',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users', // Ensures the email is unique in the users table
                function ($attribute, $value, $fail) use ($domainRegex) {
                    // Validates the email domain against the allowed domains
                    if (!preg_match('/@(' . $domainRegex . ')$/', $value)) {
                        $fail('The email address must be one of the following domains: ' . str_replace('|', ', ', $domainRegex));
                    }
                }
            ],
            'nationality' => 'required|string|max:255', // Validation for nationality
            'bu_id' => 'required|integer', // Validation for Business Unit
            'country_id' => 'required|integer', // Validation for Country
        ]);

        // Insert into the users table
        $userId = DB::table('users')->insertGetId([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'role' => $request->input('role'),
            'password' => bcrypt('default'), // Password is hashed before saving
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Get the authenticated head ID
        $headId = Auth::id();

        // Insert into the buh table, including the head_id
        $buhId = DB::table('buh')->insertGetId([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'nationality' => $validatedData['nationality'],
            'head_id' => $headId, // Automatically assign the head_id
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert into the bu_country table
        DB::table('bu_country')->insert([
            'buh_id' => $buhId,
            'bu_id' => $validatedData['bu_id'],
            'country_id' => $validatedData['country_id'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'User added successfully!');
    }

    public function deleteBUH($id){
        DB::beginTransaction(); // Start a database transaction
    
        try {
            // Fetch the related buh_id and email using the provided id from bu_country
            $buCountry = DB::table('bu_country as bc')
                ->join('buh as b', 'bc.buh_id', '=', 'b.id')
                ->select('bc.buh_id', 'b.email') // Select the email from the buh table
                ->where('bc.id', $id)
                ->first();
    
            if (!$buCountry) {
                return redirect()->route('admin#view-buh')->with('error', 'User not found.');
            }
    
            $buhId = $buCountry->buh_id;
    
            // Delete related entries in the sale_agent table first
            DB::table('sale_agent')->where('bu_country_id', $id)->delete();
    
            // Delete the entry in the bu_country table
            DB::table('bu_country')->where('id', $id)->delete();
    
            // Then delete the entry in the buh table
            DB::table('buh')->where('id', $buhId)->delete();
    
            // Finally, delete the entry in the users table using the retrieved email
            $user = DB::table('users')->where('email', $buCountry->email)->first();
            if ($user) {
                DB::table('users')->where('id', $user->id)->delete();
            } else {
                Log::warning('User not found for deletion with email: ' . $buCountry->email);
            }
    
            DB::commit(); // Commit the transaction
    
            return redirect()->back()->with('success', 'BUH deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback the transaction on error
            Log::error('Error deleting BUH: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete BUH.');
        }
    }
    
    public function updateStatusSaleAgent(Request $request, $owner_pid)
    
    {
        // Log incoming request data
        Log::info('Update Status Request:', [
            'owner_pid' => $owner_pid,
            'request_data' => $request->all()
        ]);

        try {
            // Retrieve the owner by their primary ID (assuming owner_pid is the primary key)
            $owner = SaleAgent::find($owner_pid);

            if ($owner) {
                // Update the status
                $owner->status = $request->input('status');
                $owner->save();

                Log::info('Owner status updated successfully:', [
                    'owner_id' => $owner->id,
                    'new_status' => $owner->status
                ]);

                // Return a success message as JSON
                return response()->json(['message' => 'Owner status updated successfully.']);
            } else {
                Log::warning('Owner not found:', ['owner_pid' => $owner_pid]);

                return response()->json(['message' => 'Owner not found.'], 404);
            }
        } catch (\Exception $e) {
            // Log the exception for debugging
            Log::error('Error updating owner status: ' . $e->getMessage());

            // Return an error message as JSON
            return response()->json(['message' => 'An unexpected error occurred. Please try again later.'], 500);
        }
    }

    public function getProgress(){
        return response()->json(['progress' => Session::get('progress', 0)]);
    }

    public function transfer(Request $request)
    {
        set_time_limit(300);
        $owner_pid = $request->input('owner_pid');
        try {
            // Validate the input
            $validated = $request->validate([
                'transferMethod' => 'required|string',
                'contact_pid' => 'nullable|array',
                'contact_pid.*' => ['required', 'string'],
            ]);

            // Determine the selected transfer method
            $transferMethod = $validated['transferMethod'];
            $selectedContacts = $validated['contact_pid'] ?? [];

            Log::info('Selected Transfer Method: ' . $transferMethod);
            Log::info('Owner PID: ' . $owner_pid);

            // **Check if the sales agent is inactive**
            $owner = Owner::where('owner_pid', $owner_pid)->first();

            Log::info("Owner Status: " . $owner->status);
            if ($owner->status === 'active') {
                Log::error('Sales agent is inactive or not found. Transfer cannot proceed.', ['owner_pid' => $owner_pid]);
                return redirect()->back()->with('error', 'The selected sales agent status is active. Please deactivate sales agent.');
            }

            // If "Select all Contacts" is chosen, get all contact PIDs associated with the provided owner_pid
            if ($transferMethod === 'Select all Contacts') {
                $selectedContacts = Contact::where('fk_contacts__owner_pid', $owner_pid)->pluck('contact_pid')->toArray();
            }

            // Check if there are any contacts selected
            if (empty($selectedContacts)) {
                Log::info('No contacts selected for transfer.');
                return redirect()->back()->with('warning', 'Please select at least one contact to transfer.');
            }

            $batchSize = 100; // Define the batch size
            $totalContacts = count($selectedContacts);
            $processedContacts = 0; // Track the number of processed contacts
            Session::put('progress', 0); // Initialize progress to 0

            foreach (array_chunk($selectedContacts, $batchSize) as $contactsBatch) {
                foreach ($contactsBatch as $contact_pid) {
                    // Find the contact in the contacts table
                    $contact = Contact::where('contact_pid', $contact_pid)
                        ->where('fk_contacts__owner_pid', $owner_pid)
                        ->first();

                    if ($contact) {
                        Log::info('Processing contact PID: ' . $contact_pid);
                        Log::info('Contact before move: ', $contact->toArray());

                        try {
                            // Move the contact to moved_contacts table
                            $movedContact = new MovedContact();
                            $movedContact->fk_contacts__owner_pid = null;
                            $movedContact->date_of_allocation = $contact->date_of_allocation;
                            $movedContact->name = $contact->name;
                            $movedContact->email = $contact->email;
                            $movedContact->contact_number = $contact->contact_number;
                            $movedContact->address = $contact->address;
                            $movedContact->country = $contact->country;
                            $movedContact->qualification = $contact->qualification;
                            $movedContact->job_role = $contact->job_role;
                            $movedContact->company_name = $contact->company_name;
                            $movedContact->skills = $contact->skills;
                            $movedContact->social_profile = $contact->social_profile;
                            $movedContact->status = $contact->status;
                            $movedContact->source = $contact->source;
                            $movedContact->datetime_of_hubspot_sync = $contact->datetime_of_hubspot_sync;
                            $movedContact->save();

                            // Delete the original contact
                            $contact->delete();

                            Log::info('Contact PID: ' . $contact_pid . ' moved to moved_contacts table.');
                        } catch (\Exception $e) {
                            Log::error('Failed to move contact PID: ' . $contact_pid . ' - Error: ' . $e->getMessage());
                        }

                        // Update the processed contacts count
                        $processedContacts++;
                        // Update progress after processing each contact
                        $progress = intval(($processedContacts / $totalContacts) * 100);
                        Session::put('progress', $progress);
                        Log::info('Progress updated to: ' . $progress);
                    } else {
                        Log::warning('Contact PID: ' . $contact_pid . ' not found in the contacts table.');
                    }
                }
                // Simulate processing delay
                sleep(1);
            }

            // Ensure progress reaches 100% after completion
            Session::put('progress', 100);

            // Instantiate the RoundRobinAllocator
            $allocator = new RoundRobinAllocator();
            // Call the assignContacts method to assign contacts back to the contacts table using round-robin
            $this->assignContacts($allocator);
            Log::info('Contacts successfully moved.');
            return redirect()->back()->with('success', 'Contacts successfully moved to the moved_contacts table.');
        } catch (ValidationException $e) {
            Log::error("Validation error during contact transfer: " . $e->getMessage());
            return redirect()->back()->with('error', 'Contact transfer failed.');
        } catch (\Exception $e) {
            Log::error("General error during contact transfer: " . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->with('error', 'An unexpected error occurred.' . $e);
        }
    }

    public function assignContacts(RoundRobinAllocator $allocator)
    {
        set_time_limit(300);
        try {
            // Retrieve all contacts from moved_contacts table
            $movedContacts = MovedContact::all();

            // Check if there are contacts to move
            if ($movedContacts->isEmpty()) {
                Log::warning('No contacts found in MovedContacts table.');
                return redirect()->back()->with('warning', 'No contacts found to assign.');
            }

            // Loop through each contact and move to Contacts table
            foreach ($movedContacts as $movedContact) {
                // Create a new Contact instance
                $contact = new Contact();
                $contact->name = $movedContact->name;
                $contact->email = $movedContact->email;
                $contact->contact_number = $movedContact->contact_number;
                $contact->address = $movedContact->address;
                $contact->country = $movedContact->country;
                $contact->qualification = $movedContact->qualification;
                $contact->job_role = $movedContact->job_role;
                $contact->company_name = $movedContact->company_name;
                $contact->skills = $movedContact->skills;
                $contact->social_profile = $movedContact->social_profile;
                $contact->status = $movedContact->status;
                $contact->source = $movedContact->source;
                $contact->datetime_of_hubspot_sync = $movedContact->datetime_of_hubspot_sync;

                // Save the new contact to the Contacts table
                if ($contact->save()) {
                    Log::info('Contact moved successfully: ' . $contact->name);

                    // Delete the moved contact from the MovedContacts table
                    $movedContact->delete();
                    Log::info('Moved contact deleted: ' . $movedContact->name);
                } else {
                    Log::warning('Failed to save moved contact: ' . $contact->name);
                }
            }

            // After moving contacts, call the allocate method to assign contacts using round-robin
            $allocator->allocate(1, 1); // hardcode for testing purposed
            // If allocation is successful, redirect back with a success message
            return redirect()->back()->with('success', 'Contacts successfully assigned.');
        } catch (\Exception $e) {
            // If there is an error during allocation, log the error and redirect back with an error message
            Log::error('Failed to assign contacts: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to assign contacts. Please try again.');
        }
    }
}
