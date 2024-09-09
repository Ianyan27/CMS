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
use App\Models\Contact;
use App\Models\ContactArchive;
use App\Models\ContactDiscard;
use App\Models\Log as ModelsLog;
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
            'platform' => 'required|string'
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

        // Get the BUH ID from the logged-in user
        $buhId = Auth::user()->id;

        // Retrieve owners (sales agents) under the specified BUH
        $owners = Owner::where('fk_buh', $buhId)->get();
        Log::info('Total owners retrieved for BUH ID ' . $buhId . ':', ['count' => $owners->count()]);

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
            $import = new ContactsImport($platform);
            Excel::import($import, $file);
            $allocator = new RoundRobinAllocator();
            $allocator->allocate();
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
        $invalidRows = $import->getInvalidRows();
        $duplicateRows = $import->getDuplicateRows();

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


        return response()->json([
            'success' => true,
            'message' => 'Import completed.',
            'data' => [
                'valid_count' => $validCount,
                'invalid_count' => $invalidCount,
                'duplicate_count' => $duplicateCount,
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

    public function saveUser(Request $request){

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
            'fk_buh' => 'required|integer',
        ]);

        Log::info('Processing user registration for email: ' . $request->input('email'));

        if ($validator->fails()) {
            Log::error('Validation failed for email: ' . $request->input('email'), $validator->errors()->toArray());
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

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
            $saleAgent = Owner::create([
                'owner_name' => $request->input('agentName'),
                'owner_email_id' => $request->input('email'),
                'fk_buh' => $request->input('fk_buh'),
                'owner_hubspot_id' => $request->input('hubspotId'),
                'owner_business_unit' => $request->input('businessUnit'),
                'country' => $request->input('country'),
                'owner_pid' => $user->id,
                'status' => 'active', // Setting status to active
            ]);

            DB::commit();

            Log::info('Successfully saved user and sale agent for email: ' . $user->email);

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

    public function transferContact($owner_pid) {
        Session::put('progress', 0);
        $owner = Owner::where('owner_pid', $owner_pid)->first();
        $contacts = Contact::where('fk_contacts__owner_pid', $owner_pid)->get();
        $archivedContacts = ContactArchive::where('fk_contact_archives__owner_pid', $owner_pid)->get();
        $discardedContacts = ContactDiscard::where('fk_contact_discards__owner_pid', $owner_pid)->get();
        
        $allContacts = $contacts->concat($archivedContacts)->concat($discardedContacts);
        $countAllContacts = $allContacts->count();
        
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
            'countAllContacts' => $countAllContacts
        ]);
    }


    public function transfer(Request $request){
        set_time_limit(300);
        $owner_pid = $request->input('owner_pid');
        try {
            // Validate the input
            $validated = $request->validate([
                'transferMethod' => 'required|string',
                'contact_pid' => 'nullable|array',
                'contact_pid.*' => ['required', 'string', new ContactExistInAnyTable],
            ]);

            // Determine the selected transfer method
            $transferMethod = $validated['transferMethod'];
            $selectedContacts = $validated['contact_pid'] ?? [];

            Log::info('Selected Transfer Method: ' . $transferMethod);
            Log::info('Owner PID: ' . $owner_pid);

            // If "Select all Contacts" is chosen, get all contact PIDs associated with the provided owner_pid
            if ($transferMethod === 'Select all Contacts') {
                $selectedContacts = Contact::where('fk_contacts__owner_pid', $owner_pid)->pluck('contact_pid')
                    ->merge(ContactArchive::where('fk_contact_archives__owner_pid', $owner_pid)->pluck('contact_archive_pid'))
                    ->merge(ContactDiscard::where('fk_contact_discards__owner_pid', $owner_pid)->pluck('contact_discard_pid'))
                    ->toArray();
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
                    // Search for the contact in all three tables, filtering by owner_pid
                    Log::info('Searching for contact PID: ' . $contact_pid);
                    $contact = Contact::where('contact_pid', $contact_pid)
                                        ->where('fk_contacts__owner_pid', $owner_pid)
                                        ->first()
                                    ?? ContactArchive::where('contact_archive_pid', $contact_pid)
                                                    ->where('fk_contact_archives__owner_pid', $owner_pid)
                                                    ->first()
                                    ?? ContactDiscard::where('contact_discard_pid', $contact_pid)
                                                    ->where('fk_contact_discards__owner_pid', $owner_pid)
                                                    ->first();

                    if ($contact) {
                        Log::info('Processing contact PID: ' . $contact_pid);
                        Log::info('Contact before update: ', $contact->toArray());

                        // Move the contact based on its status
                        try {
                            if ($contact instanceof Contact) {
                                if (in_array($contact->status, ['New', 'InProgress', 'HubSpot Contact'])) {
                                    $contact->fk_contacts__owner_pid = null;
                                    $contact->save();
                                    Log::info('Contact PID: ' . $contact_pid . ' owner removed.');
                                } else{
                                    throw new \Exception('Unexpected status: ' . $contact->status);
                                }
                            } elseif ($contact instanceof ContactArchive) {
                                $transferArchive = new Contact();
                                $transferArchive->fk_contacts__owner_pid = null;
                                $transferArchive->name = $contact->name;
                                $transferArchive->email = $contact->email;
                                $transferArchive->contact_number = $contact->contact_number;
                                $transferArchive->address = $contact->address;
                                $transferArchive->qualification = $contact->qualification;
                                $transferArchive->country = $contact->country;
                                $transferArchive->job_role = $contact->job_role;
                                $transferArchive->company_name = $contact->qualification;
                                $transferArchive->skills = $contact->skills;
                                $transferArchive->social_profile = $contact->social_profile;
                                $transferArchive->source = $contact->source;
                                $transferArchive->datetime_of_hubspot_sync = $contact->datetime_of_hubspot_sync;
                                $transferArchive->status = $contact->status;

                                $transferArchive->save();

                                $contact->delete();

                                Log::info('Contact PID: ' . $contact_pid . ' owner removed from archive/discard.');
                            } else{
                                $transferArchive = new Contact();
                                $transferArchive->fk_contacts__owner_pid = null;
                                $transferArchive->name = $contact->name;
                                $transferArchive->email = $contact->email;
                                $transferArchive->contact_number = $contact->contact_number;
                                $transferArchive->address = $contact->address;
                                $transferArchive->qualification = $contact->qualification;
                                $transferArchive->country = $contact->country;
                                $transferArchive->job_role = $contact->job_role;
                                $transferArchive->company_name = $contact->qualification;
                                $transferArchive->skills = $contact->skills;
                                $transferArchive->social_profile = $contact->social_profile;
                                $transferArchive->source = $contact->source;
                                $transferArchive->datetime_of_hubspot_sync = $contact->datetime_of_hubspot_sync;
                                $transferArchive->status = $contact->status;
                                $transferArchive->save();
                                $contact->delete();
                                Log::info('Contact PID: ' . $contact_pid . ' owner removed from archive/discard.');
                            }
                        } catch (\Exception $e) {
                            Log::error('Failed to process contact PID: ' . $contact_pid . ' - Error: ' . $e->getMessage());
                        }

                        // Update the processed contacts count
                        $processedContacts++;
                        // Update progress after processing each contact
                        $progress = intval(($processedContacts / $totalContacts) * 100);
                        Session::put('progress', $progress);
                        Log::info('Progress updated to: ' . $progress);
                    } else {
                        Log::warning('Contact PID: ' . $contact_pid . ' not found in any table.');
                    }
                }
                // Simulate processing delay
                sleep(1);
            }

            // Ensure progress reaches 100% after completion
            Session::put('progress', 100);

            // Update the owner's total assigned contacts
            $totalAssignedContacts = Contact::where('fk_contacts__owner_pid', $owner_pid)->count()
                + ContactArchive::where('fk_contact_archives__owner_pid', $owner_pid)->count()
                + ContactDiscard::where('fk_contact_discards__owner_pid', $owner_pid)->count();
            Owner::where('owner_pid', $owner_pid)->update(['total_assign_contacts' => $totalAssignedContacts]);
            Log::info('Contact Successfully removed/moved');
            return redirect()->back()->with('success', 'Contacts successfully removed/moved and owner\'s total assigned contacts updated.');
        } catch (ValidationException $e) {
            Log::error("Validation error during contact transfer: " . $e->getMessage());
            return redirect()->back()->with('error', 'Contact transfer failed.');
        } catch (\Exception $e) {
            Log::error("General error during contact transfer: " . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->with('error', 'An unexpected error occurred.');
        }
    }

    public function assignContacts(RoundRobinAllocator $allocator){
        set_time_limit(300);
        try {
            // Call the allocate method to assign contacts
            $allocator->allocate();
            // If allocation is successful, redirect back with a success message
            return redirect()->back()->with('success', 'Contacts successfully assigned.');
        } catch (\Exception $e) {
            // If there is an error during allocation, log the error and redirect back with an error message
            Log::error('Failed to assign contacts: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to assign contacts. Please try again.');
        }
    }


    public function updateStatusOwner($owner_pid){
        try {
            // Retrieve the owner by their primary ID (assuming owner_pid is the primary key)
            $owner = Owner::find($owner_pid);

            if ($owner) {
                if ($owner->status === 'active') {
                    $owner->status = 'inactive';
                    $message = 'Owner status updated to inactive';
                } else {
                    $owner->status = 'active';
                    $message = 'Owner status updated to active';
                }
                $owner->save();
                return redirect()->back()->with('success', $message);
            } else {
                return redirect()->back()->with('error', 'Owner not found');
            }
        } catch (\Exception $e) {
            // Log the exception for debugging
            Log::error('Error updating owner status: ' . $e->getMessage());

            // Redirect back with a generic error message
            return redirect()->back()->with('error', 'An unexpected error occurred. Please try again later.');
        }
    }




    public function getProgress() {
        return response()->json(['progress' => Session::get('progress', 0)]);
    }
    
}
