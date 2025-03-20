<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use App\Models\CSVImport;
use Illuminate\Support\Facades\Auth;

class ImportCSV extends Controller
{
    /**
     * Step 1: Preview CSV Import
     *
     * This method saves the uploaded CSV file to the database,
     * parses it into valid, invalid, and duplicate records,
     * and stores these arrays in the session.
     */
    public function importPreview(Request $request)
    {
        $user = Auth::user();
        // Validate file type and size.
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048'
        ]);

        $file = $request->file('file');

        // Open the file for processing using its temporary location.
        if (($handle = fopen($file->getRealPath(), 'r')) === false) {
            return redirect()->back()->with('error', 'Unable to open the file.');
        }

        // Read header row.
        $header = fgetcsv($handle, 1000, ',');

        // Minimal header check.
        $requiredFields = ['hubspot_id', 'firstname', 'lastname', 'email'];
        $missing = array_diff($requiredFields, $header);
        if (!empty($missing)) {
            fclose($handle);
            return redirect()->back()->with('error', 'Invalid CSV. Missing header(s): ' . implode(', ', $missing));
        }

        // Arrays to hold records.
        $validRecords   = [];
        $invalidRecords = [];
        $rowNumber      = 1; // header is row 1

        // Process each row.
        while (($rowData = fgetcsv($handle, 1000, ',')) !== false) {
            $rowNumber++;
            // Map row data to header columns.
            $row = array_combine($header, $rowData);

            // Basic validations for required fields and email format.
            $errors = [];
            foreach ($requiredFields as $field) {
                if (empty($row[$field])) {
                    $errors[] = "Missing $field";
                }
            }
            if (!empty($row['email']) && !filter_var($row['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Invalid email format";
            }

            if (empty($errors)) {
                // Build a record for valid rows.
                $validRecords[] = [
                    'hubspot_id'         => $row['hubspot_id'],
                    'firstname'          => $row['firstname'],
                    'lastname'           => $row['lastname'],
                    'email'              => $row['email'],
                    'gender'             => $row['gender'] ?? null,
                    'hubspot_created_at' => isset($row['createdate']) ? Carbon::parse($row['createdate']) : null,
                    'hubspot_updated_at' => isset($row['lastmodifieddate']) ? Carbon::parse($row['lastmodifieddate']) : null,
                    'phone'              => $row['phone'] ?? null,
                    'hubspot_owner_id'   => $row['hubspot_owner_id'] ?? null,
                    'hs_lead_status'     => $row['hs_lead_status'] ?? null,
                    'company'            => $row['company'] ?? null,
                    'lifecyclestage'     => $row['lifecyclestage'] ?? null,
                    'country'            => $row['country'] ?? null,
                    // Randomly assign marked_deleted ("yes" or "no")
                    'marked_deleted'     => (rand(0, 1) === 1) ? 'yes' : 'no',
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ];
            } else {
                // If invalid, add error details and row number.
                $invalidRecords[] = array_merge($row, [
                    'row_number'   => $rowNumber,
                    'error_reason' => implode('; ', $errors),
                ]);
            }
        }
        fclose($handle);

        // Check for duplicates among valid records.
        $hubspotIds = array_column($validRecords, 'hubspot_id');
        $existingIds = DB::table('hubspot_contacts')
            ->whereIn('hubspot_id', $hubspotIds)
            ->pluck('hubspot_id')
            ->toArray();

        $duplicateRecords = [];
        $finalValidRecords = [];
        foreach ($validRecords as $record) {
            if (in_array($record['hubspot_id'], $existingIds)) {
                // Mark duplicates with an error reason.
                $record['error_reason'] = 'Duplicate: hubspot_id already exists';
                $duplicateRecords[] = $record;
            } else {
                $finalValidRecords[] = $record;
            }
        }

        // If no valid records exist, store summary and inform the user.
        if (count($finalValidRecords) == 0) {
            $summary = [
                'valid_count'     => 0,
                'invalid_count'   => count($invalidRecords),
                'duplicate_count' => count($duplicateRecords),
                'message'         => 'No new valid records found. All records are either duplicates or invalid.'
            ];
            Session::put('import_summary', $summary);
            return redirect()->back()->with('error', 'The CSV file does not contain any new valid records. Please review the summary for details.');
        }

        // Generate a "clean" CSV content from the final valid records.
        // We'll use the keys from the first valid record as the header.
        $handleTemp = fopen('php://temp', 'r+');
        $csvHeaders = array_keys($finalValidRecords[0]);
        fputcsv($handleTemp, $csvHeaders);
        foreach ($finalValidRecords as $record) {
            $rowData = [];
            foreach ($csvHeaders as $headerKey) {
                $rowData[] = $record[$headerKey] ?? '';
            }
            fputcsv($handleTemp, $rowData);
        }
        rewind($handleTemp);
        $cleanedCSV = stream_get_contents($handleTemp);
        fclose($handleTemp);

        // Save the cleaned CSV file to the database.
        // We use a new file name (with a "cleaned_" prefix) so users know it is processed.
        $timestamp = now()->format('Y-m-d-H-i-s');
        $cleanFileName = 'imported-file.' . $timestamp . '.csv';

        $csvImport =CSVImport::create([
            'file_name'    => $cleanFileName,
            'file_content' => $cleanedCSV,
            'user_id'      => $user->id ?? null,
        ]);

        // Store the arrays in session for preview/download functionality.
        Session::put('valid_records', $finalValidRecords);
        Session::put('invalid_records', $invalidRecords);
        Session::put('duplicate_records', $duplicateRecords);

        // Prepare a summary for the modal.
        $summary = [
            'valid_count'     => count($finalValidRecords),
            'invalid_count'   => count($invalidRecords),
            'duplicate_count' => count($duplicateRecords),
        ];
        Session::put('import_summary', $summary);

        return redirect()->back()->with('info', 'Preview ready. Please review the modal.');
    }


    /**
     * Download CSV from session data for Valid Records.
     */
    public function downloadValidRecords()
    {
        $validRecords = Session::get('valid_records', []);
        return $this->downloadCsvFromArray($validRecords, 'valid_records.csv');
    }

    /**
     * Download CSV from session data for Invalid Records.
     */
    public function downloadInvalidRecords()
    {
        $invalidRecords = Session::get('invalid_records', []);
        return $this->downloadCsvFromArray($invalidRecords, 'invalid_records.csv');
    }

    /**
     * Download CSV from session data for Duplicate Records.
     */
    public function downloadDuplicateRecords()
    {
        $duplicateRecords = Session::get('duplicate_records', []);
        return $this->downloadCsvFromArray($duplicateRecords, 'duplicate_records.csv');
    }

    /**
     * Utility method to generate a CSV response from an array.
     */
    private function downloadCsvFromArray(array $records, string $filename)
    {
        if (empty($records)) {
            return redirect()->back()->with('error', 'No records to download.');
        }

        // Get all keys from the first record.
        $headers = array_keys($records[0]);

        // Create CSV in memory.
        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, $headers);
        foreach ($records as $row) {
            $rowData = [];
            foreach ($headers as $key) {
                $rowData[] = $row[$key] ?? '';
            }
            fputcsv($handle, $rowData);
        }
        rewind($handle);
        $csvContent = stream_get_contents($handle);
        fclose($handle);

        return response($csvContent, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ]);
    }

    /**
     * Sync valid records into the database.
     */
    public function syncValidRecords()
    {
        $validRecords = Session::get('valid_records', []);
        if (empty($validRecords)) {
            return redirect()->back()->with('error', 'No valid records found to sync.');
        }

        // Insert valid records into the hubspot_contacts table.
        DB::table('hubspot_contacts')->insertOrIgnore($validRecords);

        // Clear session data.
        Session::forget('valid_records');
        Session::forget('invalid_records');
        Session::forget('duplicate_records');
        Session::forget('import_summary');

        return redirect()->back()->with('success', 'Successfully synced valid contacts to the database.');
    }
}
