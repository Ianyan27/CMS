<?php

namespace App\Http\Controllers;

use App\Models\Owner;
use App\Models\User;
use App\Services\RoundRobinAllocator;
use Illuminate\Http\Request;
use App\Imports\ContactsImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BUHController extends Controller
{

    public function index(){
        return view('csv_import_form');
    }

    public function import(Request $request){
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

    private function exportCsv($fileName, $data){
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

    private function arrayToCsv(array $array){
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

    public function saveUser(Request $request) {
        $allowedDomains = ['lithan.com', 'educlaas.com', 'learning.educlaas.com'];
        $domainRegex = implode('|', array_map(function ($domain) {
            return preg_quote($domain, '/');
        }, $allowedDomains));
    
        $validator = Validator::make($request->all(), [
            'agentName' => 'required|string|max:255',
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
            'hubspotId' => 'required|string|max:100',
            'businessUnit' => 'required|string|max:255',
            'country' => 'required|string|max:100',
            'fk_buh' => 'required|integer'
        ]);
    
        Log::info('Processing user registration for email: ' . $request->input('email'));
    
        if ($validator->fails()) {
            Log::error('Validation failed for email: ' . $request->input('email'));
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
    
        DB::beginTransaction();
    
        try {
            $user = User::create([
                'name' => $request->input('agentName'),
                'email' => $request->input('email'),
                'role' => $request->input('role')
            ]);
    
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
            return redirect()->back()->with('error', 'Failed to add Sale Agent. Please try again.');
        }
    }    
    public function deleteOwner($owner_pid){
        DB::beginTransaction();
        try {
            // Delete the Owner record
            $owner = Owner::where('owner_pid', $owner_pid)->first();
            
            if (!$owner) {
                return redirect()->back()->with('error', 'Owner not found.');
            }
    
            // Delete the associated User record
            User::where('id', $owner_pid)->delete();
    
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
}
