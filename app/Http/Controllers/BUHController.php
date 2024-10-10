<?php

namespace App\Http\Controllers;

use App\Models\ArchiveLogs;
use App\Models\Inactive_Owners;
use App\Models\Owner;
use App\Models\User;
use App\Rules\ContactExistInAnyTable;
use App\Services\RoundRobinAllocator;
use Illuminate\Http\Request;
use App\Imports\ContactsImport;
use App\Models\BuCountry;
use App\Models\BUH;
use App\Models\Contact;
use App\Models\ContactArchive;
use App\Models\ContactDiscard;
use App\Models\Country;
use App\Models\Log as ModelsLog;
use App\Models\MovedContact;
use App\Models\SaleAgent;
use App\Models\TransferContacts;
use Illuminate\Pagination\LengthAwarePaginator;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class BUHController extends Controller
{

    public function index()
    {
        return view('csv_import_form');
    }

    public function import(Request $request)
    {
        set_time_limit(300);
        // Validate the uploaded file
        $fileValidator = Validator::make($request->all(), [
            'csv_file' => 'required|mimes:csv,txt|max:102400',
            'platform' => 'required|string',
        ], [
            'csv_file.required' => 'The CSV file is required.',
            'csv_file.mimes' => 'The uploaded file must be a file of type: csv',
            'csv_file.max' => 'The uploaded file may not be greater than 100MB.',
            'platform.required' => 'Source is required.'
        ]);

        if ($fileValidator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed for the uploaded file.',
                'errors' => $fileValidator->errors()
            ], 422);
        }

        $file = $request->file('csv_file');
        $platform = $request->input('platform'); // Get the platform value
        $country = $request->input('country');
        $bu = $request->input('bu');
        $buh = $request->input('buh');

        Log::info('Received country: ' . $request->input('country'));
        Log::info('Received BUH: ' . $request->input('buh'));


        Log::info('reterived bu ' . $bu . ' buh ' . $buh);

        // Retrieve Country ID based on Country Name
        $country = Country::where('name', $country)->first();
        if (!$country) {
            return response()->json([
                'success' => false,
                'message' => "Country not found."
            ], 404);
        }
        $countryId = $country->id;

        // Retrieve BU ID based on BUH Name
        $buh = BUH::where('name', $buh)->first();
        if (!$buh) {
            return response()->json([
                'success' => false,
                'message' => "Business Unit Head not found."
            ], 404);
        }
        $buhId = $buh->id;

        // Retrieve owners (sales agents) under the specified BUH

        $bu_country = BuCountry::where('buh_id', $buhId)->first();
        $bu_country_id = $bu_country->id;
        Log::info("bu country id " . $bu_country_id);
        $owners = SaleAgent::where('bu_country_id', $bu_country_id)->get();
        Log::info('Total owners retrieved for BUH_Country ID ' . $bu_country->id . ':', ['count' => $owners->count()]);

        if ($owners->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => "No sales agent is assigned. Please make sure to assign the appropriate sales agents to continue."
            ], 500);
        }

        try {
            // Store the file in public storage
            //$filePath = Storage::disk('public')->putFile('csv_uploads', $file);
            // Import the data into the database using the ContactsImport class
            $import = new ContactsImport($platform, $country->name);
            Excel::import($import, $file);
            $allocator = new RoundRobinAllocator();
            $allocator->allocate($buhId, $country->name);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to import data: ' . $e->getMessage()
            ], 500);
        }

        // Get the import results
        $validCount = $import->getValidCount();
        $invalidCount = $import->getInvalidCount();
        $duplicateCount = $import->getDuplicateCount();
        $unselectedCountryCount = $import->getUnselectedCountryCount();
        $invalidRows = $import->getInvalidRows();
        $duplicateRows = $import->getDuplicateRows();
        $unselectedCountryRows = $import->getUnselectedCountryRows();

        // Export invalid and duplicate rows
        $fileLinks = [];

        if (!empty($invalidRows)) {
            // Use the first row of the invalidRows as the header if available
            $headers = array_keys($invalidRows[0]);
            $headers[] = 'validation_errors'; // Add validation errors column
            $invalidCsvData = array_merge([$headers], $invalidRows);
            $invalidCsvFileName = 'invalid_rows.csv';
            $invalidCsvUrl = $this->exportCsv($invalidCsvFileName, $invalidCsvData);
            $fileLinks['invalid_rows'] = $invalidCsvUrl;
        }

        if (!empty($duplicateRows)) {
            // Use the first row of the duplicateRows as the header if available
            $headers = array_keys($duplicateRows[0]);
            $duplicateCsvData = array_merge([$headers], $duplicateRows);
            $duplicateCsvFileName = 'duplicate_rows.csv';
            $duplicateCsvUrl = $this->exportCsv($duplicateCsvFileName, $duplicateCsvData);
            $fileLinks['duplicate_rows'] = $duplicateCsvUrl;
        }
        // Export unselected country rows (new logic)
        if (!empty($unselectedCountryRows)) {
            $headers = array_keys($unselectedCountryRows[0]);
            $headers[] = 'validation_errors';
            $unselectedCountryCsvData = array_merge([$headers], $unselectedCountryRows);
            $unselectedCountryCsvFileName = 'unselected_country_rows.csv';
            $unselectedCountryCsvUrl = $this->exportCsv($unselectedCountryCsvFileName, $unselectedCountryCsvData);
            $fileLinks['unselected_country_rows'] = $unselectedCountryCsvUrl;
        }


        return response()->json([
            'success' => true,
            'message' => 'Import completed.',
            'data' => [
                'valid_count' => $validCount,
                'invalid_count' => $invalidCount,
                'duplicate_count' => $duplicateCount,
                'unselected_country_count' => $unselectedCountryCount,
                'file_links' => $fileLinks
                // 'uploaded_file_path' => Storage::url($filePath) // Provide URL to the uploaded file
            ]
        ]);
    }

    private function exportCsv($fileName, $data)
    {
        try {
            $csvContent = $this->arrayToCsv($data);
            // Save the file to the 'public' disk
            Storage::disk('public')->put($fileName, $csvContent);

            // Generate a public URL
            return Storage::url($fileName);
        } catch (\Exception $e) {

            throw new \Exception("Failed to export $fileName: " . $e->getMessage());
        }
    }

    private function arrayToCsv(array $array)
    {
        $csv = fopen('php://temp', 'r+');

        foreach ($array as $row) {
            // Flatten the row to ensure no nested arrays
            $flattenedRow = array_map(function ($value) {
                if (is_array($value)) {
                    // Convert array to a JSON string or serialize it if necessary
                    return json_encode($value);
                }
                return $value;
            }, $row);

            fputcsv($csv, $flattenedRow);
        }

        rewind($csv);
        return stream_get_contents($csv);
    }

    public function saveUser(Request $request)
    {

        $allowedDomains = ['lithan.com', 'educlaas.com', 'learning.educlaas.com'];
        $domainRegex = implode('|', array_map(function ($domain) {
            return preg_quote($domain, '/');
        }, $allowedDomains));

        // Validation rules
        $validator = Validator::make($request->all(), [
            'agentName' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                function ($attribute, $value, $fail) use ($domainRegex) {
                    if (!preg_match('/@(' . $domainRegex . ')$/', $value)) {
                        $fail('The email address must be one of the following domains: ' . str_replace('|', ', ', $domainRegex));
                    }
                }
            ],
            'hubspotId' => 'required|string|max:100',
            'businessUnit' => 'required|string|max:255',
            'country' => 'required|string|max:100',
            'email_buh' => 'required|string',
        ]);

        Log::info('Processing user registration for email: ' . $request->input('email'));

        if ($validator->fails()) {
            Log::error('Validation failed for email: ' . $request->input('email'), $validator->errors()->toArray());
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // getting bu_country id
        $buh = BUH::where("email", $request->input('email_buh'))->get()->first();
        $bu_country = BuCountry::where("buh_id", $buh->id)->get()->first();

        // Begin a database transaction
        DB::beginTransaction();

        try {
            // Check if the email already exists for the Sales_Agent role
            $existingUser = User::where('email', $request->input('email'))->where('role', 'Sales_Agent')->first();
            if ($existingUser) {
                throw new \Exception('A Sales Agent with this email already exists.');
            }

            // Create the user
            $user = User::create([
                'name' => $request->input('agentName'),
                'email' => $request->input('email'),
                'role' => $request->input('role'),
                'password' => bcrypt($request->password), // Consider using a more secure password generator
            ]);

            Log::info("Created User ID: {$user->id} for email: " . $request->input('email'));

            // Create the owner (Sales Agent) with 'active' status
            $saleAgent = SaleAgent::create([
                'name' => $request->input('agentName'),
                'email' => $request->input('email'),
                'bu_country_id' => $bu_country->id,
                'hubspot_id' => $request->input('hubspotId'),
                'business_unit' => $request->input('businessUnit'),
                'nationality' => $request->input('country'),
                'status' => 'active', // Setting status to active
            ]);

            DB::commit();

            Log::info('Successfully saved user : ' . $user->email);
            Log::info("Successfully sale agent for email: " .  $saleAgent->email);

            return redirect()->route('owner#view')->with('success', 'Sale Agent successfully added');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to save user and sale agent for email: ' . $request->input('email') . '. Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'A user with this email already exists. Please use a different email address.')->withInput();
        }
    }

    public function deleteOwner($owner_pid)
    {
        DB::beginTransaction();
        try {
            // Delete the Owner record
            $owner = Owner::where('owner_pid', $owner_pid)->first();

            if (!$owner) {
                return redirect()->back()->with('error', 'Owner not found.');
            }
            // Finally, delete the Owner record
            $owner->delete();

            DB::commit();
            return redirect()->route('owner#view')->with('success', "Owner and associated User deleted successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete owner and user with owner_pid: ' . $owner_pid . '. Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete Owner. Please try again.');
        }
    }

    public function transferContact($owner_pid)

    {
        Session::put('progress', 0);
        $user = Auth::user();
        if ($user->role == 'BUH') {
            $contacts = Contact::where('fk_contacts__sale_agent_id', $owner_pid)->get();
            $archivedContacts = ContactArchive::where('fk_contact_archives__owner_pid', $owner_pid)->get();
            $discardedContacts = ContactDiscard::where('fk_contact_discards__owner_pid', $owner_pid)->get();
        } else {
            $contacts = Contact::get();
            $archivedContacts = ContactArchive::get();
            $discardedContacts = ContactDiscard::get();
        }
        $owner = SaleAgent::where('id', $owner_pid)->first();
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


    public function transfer(Request $request)
    {
        set_time_limit(300);
        $owner_pid = $request->input('owner_pid');
        $country = $request->input('country');
        $user = Auth::user();
        Log::info("email user: " . $user->email);
        $userId = BUH::where("email", $user->email)->get()->first();
        Log::info("user id: " . $userId->id);

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
            $owner = SaleAgent::where('id', $owner_pid)->first();

            Log::info("Owner Status: " . $owner->status);
            if ($owner->status === 'active') {
                Log::error('Sales agent is inactive or not found. Transfer cannot proceed.', ['owner_pid' => $owner_pid]);
                return redirect()->back()->with('error', 'The selected sales agent status is active. Please deactivate sales agent.');
            }

            // If "Select all Contacts" is chosen, get all contact PIDs associated with the provided owner_pid
            if ($transferMethod === 'Select all Contacts') {
                $selectedContacts = Contact::where('fk_contacts__sale_agent_id', $owner_pid)->pluck('contact_pid')->toArray();
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
                        ->where('fk_contacts__sale_agent_id', $owner_pid)
                        ->first();

                    if ($contact) {
                        Log::info('Processing contact PID: ' . $contact_pid);
                        Log::info('Contact before move: ', $contact->toArray());

                        try {
                            // Move the contact to moved_contacts table
                            $movedContact = new MovedContact();
                            $movedContact->fk_contacts__sale_agent_id = null;
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
            $this->assignContacts($allocator, $userId->id, $country);
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

    public function assignContacts(RoundRobinAllocator $allocator, $userId, $country)
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
            $allocator->allocate($userId, $country);
            // If allocation is successful, redirect back with a success message
            return redirect()->back()->with('success', 'Contacts successfully assigned.');
        } catch (\Exception $e) {
            // If there is an error during allocation, log the error and redirect back with an error message
            Log::error('Failed to assign contacts: ' . $e);
            return redirect()->back()->with('error', 'Failed to assign contacts. Please try again.');
        }
    }


    public function updateStatusOwner(Request $request, $owner_pid)

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

    public function contactsByBUH()
    {
        $user = Auth::user();

        // Fetch the BUH record associated with the logged-in user's email
        $buh = BUH::where('email', $user->email)->first();

        // Check if the BUH exists
        if (!$buh) {
            return redirect()->back()->with('error', 'No BUH found for the logged-in user.');
        }

        // Fetch all BuCountry records associated with this BUH
        $buCountries = BuCountry::where('buh_id', $buh->id)->first();

        // Check if there are any associated BuCountry records
        if (!$buCountries) {
            return redirect()->back()->with('error', 'No Business Units associated with this BUH.');
        }

        // Fetch all SaleAgents associated with the BuCountry records
        $saleAgentIds = SaleAgent::where('bu_country_id', $buCountries->id)->pluck('id');

        Log::info("Sales agent: " . $saleAgentIds);
        // Check if there are any associated SaleAgents
        if ($saleAgentIds->isEmpty()) {
            return redirect()->back()->with('error', 'No Sale Agents found for the logged-in BUH.');
        }

        // Query the contacts, archives, and discards for all the sale agents using the correct foreign key `fk_contacts__sale_agent_id`
        $contacts = Contact::whereIn('fk_contacts__sale_agent_id', $saleAgentIds)->paginate(50);
        $contactArchive = ContactArchive::whereIn('fk_contacts__sale_agent_id', $saleAgentIds)->paginate(50);
        $contactDiscard = ContactDiscard::whereIn('fk_contacts__sale_agent_id', $saleAgentIds)->paginate(50);

        Log::info("contacts: " . $contacts);
        // Pass the data to the view
        return view('Contact_Listing', [
            'buh' => $buh,
            'contacts' => $contacts,
            'contactArchive' => $contactArchive,
            'contactDiscard' => $contactDiscard
        ]);
    }



    public function getProgress()
    {
        return response()->json(['progress' => Session::get('progress', 0)]);
    }
}
