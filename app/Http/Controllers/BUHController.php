<?php

namespace App\Http\Controllers;

use App\Models\Owner;
use App\Models\User;
use App\Services\RoundRobinAllocator;
use Illuminate\Http\Request;
use App\Imports\ContactsImport;
use App\Models\Contact;
use App\Models\TransferContacts;
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
                // Uncomment and modify this line if you want to ensure email uniqueness
                // Rule::unique('users')->where(function ($query) use ($request) {
                //     return $query->where('role', 'Sales_Agent');
                // }),
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

            // Create the owner (sale agent)
            $saleAgent = Owner::create([
                'owner_name' => $request->input('agentName'),
                'owner_email_id' => $request->input('email'),
                'fk_buh' => $request->input('fk_buh'),
                'owner_hubspot_id' => $request->input('hubspotId'),
                'owner_business_unit' => $request->input('businessUnit'),
                'country' => $request->input('country'),
                'owner_pid' => $user->id
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
        $user = Auth::user()->email;
        $ownerList = Owner::all(); // Fetch all owners
        $owner = Owner::where('owner_pid', $owner_pid)->first(); // Fetch the current owner
        $viewContact = Contact::where('fk_contacts__owner_pid', $owner_pid)->paginate(50);
    
        // Debugging
        // dd($owner);
    
        return view('Transfer_Contacts_Page', [
            'user' => $user,
            'owner' => $owner,
            'ownerList' => $ownerList,
            'viewContact' => $viewContact
        ]);
    }

    public function transfer(Request $request){
        try {
            // Validate the input
            $validated = $request->validate([
                'transferMethod' => 'required|string',
                'contact_pid' => 'nullable|array',
                'contact_pid.*' => 'exists:contacts,contact_pid',
            ]);

            // Determine the selected transfer method
            $transferMethod = $validated['transferMethod'];
            $selectedContacts = $validated['contact_pid'] ?? [];

            // If "Select all Contacts" is chosen, get all contact PIDs
            if ($transferMethod === 'Select all Contacts') {
                $selectedContacts = Contact::pluck('contact_pid')->toArray();
            }

            if (empty($selectedContacts)) {
                return redirect()->back()->with('warning', 'Please select at least one contact to transfer.');
            }

            $totalContacts = count($selectedContacts);
            Session::put('progress', 0);

            foreach ($selectedContacts as $index => $contactPid) {
                $contact = Contact::where('contact_pid', $contactPid)->first();

                if ($contact) {
                    // Transfer logic
                    $transferContact = new TransferContacts();
                    $transferContact->name = $contact->name;
                    $transferContact->email = $contact->email;
                    $transferContact->contact_number = $contact->contact_number;
                    $transferContact->address = $contact->address;
                    $transferContact->country = $contact->country;
                    $transferContact->qualification = $contact->qualification;
                    $transferContact->job_role = $contact->job_role;
                    $transferContact->company_name = $contact->company_name;
                    $transferContact->skills = $contact->skills;
                    $transferContact->social_profile = $contact->social_profile;
                    $transferContact->status = $contact->status;
                    $transferContact->source = $contact->source;
                    $transferContact->datetime_of_hubspot_sync = $contact->datetime_of_hubspot_sync;
                    $transferContact->save();
                    $contact->delete();
                }

                // Update progress
                $progress = intval((($index + 1) / $totalContacts) * 100);
                Session::put('progress', $progress);
            }

            // Ensure progress reaches 100% after completion
            Session::put('progress', 100);

            return redirect()->back()->with('success', 'Contact Successfully Transferred.');
        } catch (ValidationException $e) {
            return redirect()->back()->with('error', 'Contact Transfer Failed.');
        }
    }
    
    public function getContacts($owner_pid) {
        try {
            Log::info('Transferring contacts to sales agent with PID: ' . $owner_pid);

            // Retrieve all contacts from TransferContacts table
            $transferContacts = TransferContacts::all();

            // Check if there are contacts to transfer
            if ($transferContacts->isEmpty()) {
                Log::warning('No contacts found in TransferContacts table.');
                return redirect()->back()->with('warning', 'No contacts found to transfer.');
            }

            // Loop through each contact and assign it to the selected sales agent
            foreach ($transferContacts as $transferContact) {
                // Create a new Contact instance
                $contact = new Contact();
                $contact->fk_contacts__owner_pid = $owner_pid;
                $contact->name = $transferContact->name;
                $contact->email = $transferContact->email;
                $contact->contact_number = $transferContact->contact_number;
                $contact->address = $transferContact->address;
                $contact->country = $transferContact->country;
                $contact->qualification = $transferContact->qualification;
                $contact->job_role = $transferContact->job_role;
                $contact->company_name = $transferContact->company_name;
                $contact->skills = $transferContact->skills;
                $contact->social_profile = $transferContact->social_profile;
                $contact->status = $transferContact->status;
                $contact->source = $transferContact->source;
                $contact->datetime_of_hubspot_sync = $transferContact->datetime_of_hubspot_sync;

                // Save the new contact to the Contacts table
                if ($contact->save()) {
                    Log::info('Contact transferred successfully: ' . $contact->name);

                    // Delete the transferred contact from the TransferContacts table
                    $transferContact->delete();
                    Log::info('Transferred contact deleted: ' . $transferContact->name);
                } else {
                    Log::warning('Failed to save transferred contact: ' . $contact->name);
                }
            }

            return redirect()->back()->with('success', 'Contacts assigned to sales agent successfully!');
        } catch (\Exception $e) {
            // Handle any unexpected exceptions
            Log::error('An error occurred during the contact transfer process:', ['exception' => $e->getMessage()]);
            return redirect()->back()->with('error', 'An error occurred while assigning contacts to the sales agent.');
        }
    }

}
