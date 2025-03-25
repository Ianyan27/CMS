<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use App\Models\CSVImport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ImportCSV extends Controller
{
    /**
     * Step 1: Preview CSV Import
     */
    public function importPreview(Request $request)
    {
        $user = Auth::user();

        // Basic file validation
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048'
        ]);

        $file = $request->file('file');

        if (($handle = fopen($file->getRealPath(), 'r')) === false) {
            return redirect()->back()->with('error', 'Unable to open the file.');
        }

        // Read the header row.
        $header = fgetcsv($handle, 1000, ',');

        // Read all CSV rows into an array.
        $records = [];
        while (($rowData = fgetcsv($handle, 1000, ',')) !== false) {
            // Map CSV columns to an associative array.
            $row = array_combine($header, $rowData);

            // Optionally, if your CSV might have a "marked as deleted" column:
            // $row['marked_deleted'] = isset($row['marked as deleted'])
            //     ? strtolower(trim($row['marked as deleted']))
            //     : 'no';

            $records[] = $row;
        }
        fclose($handle);

        // Collect all emails from CSV records.
        $emails = array_column($records, 'email');

        // Query the database for existing records by email
        // We'll retrieve their 'marked_deleted' value too, so we can differentiate reasons.
        // Key = email, Value = 'yes' or 'no'
        $existingRecords = DB::table('hubspot_contacts')
            ->whereIn('email', $emails)
            ->pluck('marked_deleted', 'email')
            ->toArray();

        // Arrays to hold the categorized records
        $invalidRecords   = [];
        $removedRecords   = [];
        $duplicateRecords = [];
        $validRecords     = [];

        // Track emails we've already accepted as valid in this CSV to handle duplicates
        $seenEmailsInCsv = [];

        foreach ($records as $record) {
            // 1) Check for invalid or empty email
            $email = isset($record['email']) ? trim($record['email']) : '';
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $record['removal_reason'] = 'Invalid or empty email';
                $invalidRecords[] = $record;
                continue;
            }

            // 2) Check if it already exists in the database or is marked as deleted in DB
            if (isset($existingRecords[$email])) {
                if (strtolower($existingRecords[$email]) === 'yes') {
                    $record['removal_reason'] = 'Marked as deleted in DB';
                } else {
                    $record['removal_reason'] = 'Already exists in DB';
                }
                $removedRecords[] = $record;
                continue;
            }

            // 3) Check for duplicates within this CSV
            if (isset($seenEmailsInCsv[$email])) {
                // This is a duplicate in the CSV itself
                $record['removal_reason'] = 'Duplicate email in CSV';
                $duplicateRecords[] = $record;
                continue;
            }

            // Otherwise, it's valid
            $validRecords[] = $record;
            // Mark this email as "seen"
            $seenEmailsInCsv[$email] = true;
        }

        // If no valid records remain, inform the user.
        if (empty($validRecords)) {
            Session::put('import_summary', [
                'message' => 'No new valid records found.'
            ]);
            return redirect()->back()->with('error', 'No new valid records found.');
        }

        // Generate a clean CSV from the remaining (valid) records.
        // If you have a "marked_deleted" column, remove it from the final CSV:
        $handleTemp = fopen('php://temp', 'r+');
        $csvHeaders = array_keys($validRecords[0]);

        // Example: if you want to remove 'marked_deleted' from the final file
        // if (($key = array_search('marked_deleted', $csvHeaders)) !== false) {
        //     unset($csvHeaders[$key]);
        // }
        // $csvHeaders = array_values($csvHeaders);

        // Write header row
        fputcsv($handleTemp, $csvHeaders);

        // Write data rows
        foreach ($validRecords as $record) {
            // Optionally remove 'marked_deleted'
            // unset($record['marked_deleted']);

            $rowData = [];
            foreach ($csvHeaders as $headerKey) {
                $rowData[] = $record[$headerKey] ?? '';
            }
            fputcsv($handleTemp, $rowData);
        }

        rewind($handleTemp);
        $cleanedCSV = stream_get_contents($handleTemp);
        fclose($handleTemp);

        // Check for confirmation before saving.
        if (!$request->has('confirm_import') || $request->input('confirm_import') !== 'true') {
            // Store preview data in session for later confirmation/download.
            Session::put('valid_records', $validRecords);
            Session::put('invalid_records', $invalidRecords);
            Session::put('removed_records', $removedRecords);
            Session::put('duplicate_records', $duplicateRecords);

            // Summaries for the user
            Session::put('import_summary', [
                'valid_count'     => count($validRecords),
                'invalid_count'   => count($invalidRecords),
                'removed_count'   => count($removedRecords),
                'duplicate_count' => count($duplicateRecords),
                'message'         => 'CSV import preview generated. Please confirm the import to save the file.'
            ]);

            return redirect()->back()->with('info', 'CSV import preview generated. Please confirm to save.');
        }

        // If confirmed, save the clean CSV file.
        $timestamp = now()->format('Y-m-d-H-i-s');
        $cleanFileName = 'imported-file.' . $timestamp . '.csv';

        $csvImport = CSVImport::create([
            'file_name'    => $cleanFileName,
            'file_content' => $cleanedCSV,
            'user_id'      => $user->id ?? null,
        ]);

        // Generate a download link (assumes a route named 'csv.download').
        $downloadLink = route('csv.download', ['id' => $csvImport->id]);

        // Store arrays in session for separate downloads.
        Session::put('valid_records', $validRecords);
        Session::put('invalid_records', $invalidRecords);
        Session::put('removed_records', $removedRecords);
        Session::put('duplicate_records', $duplicateRecords);

        Session::put('import_summary', [
            'valid_count'     => count($validRecords),
            'invalid_count'   => count($invalidRecords),
            'removed_count'   => count($removedRecords),
            'duplicate_count' => count($duplicateRecords),
            'download_link'   => $downloadLink,
        ]);

        return redirect()->back()
            ->with('info', 'Clean CSV ready for download.')
            ->with('download_link', $downloadLink);
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
     * Download CSV from session data for Removed Records.
     */
    public function downloadRemovedRecords()
    {
        $removedRecords = Session::get('removed_records', []);
        $processedRecords = [];

        foreach ($removedRecords as $record) {
            // Remove the marked_deleted field
            if (isset($record['marked_deleted'])) {
                unset($record['marked_deleted']);
            }
            // Ensure a removal_reason exists.
            if (!isset($record['removal_reason'])) {
                $record['removal_reason'] = 'Not specified';
            }
            $processedRecords[] = $record;
        }

        return $this->downloadCsvFromArray($processedRecords, 'removed_records.csv');
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
     *
     * Only contacts that do not exist in the database and are not marked as deleted (i.e. in the preview CSV)
     * are inserted.
     */
    public function syncValidRecords()
    {
        $validRecords = Session::get('valid_records', []);
        if (empty($validRecords)) {
            return redirect()->back()->with('error', 'No valid records found to sync.');
        }

        // Define columns that exist in hubspot_contacts
        $allowedColumns = [
            'firstname',
            'lastname',
            'email',
            'gender',
            'hubspot_created_at',
            'hubspot_updated_at',
            'phone',
            'hubspot_owner_id',
            'hs_lead_status',
            'company',
            'lifecyclestage',
            'country',
        ];

        // Filter each record to only these columns
        $filteredValidRecords = [];
        foreach ($validRecords as $record) {
            $newRecord = [];
            foreach ($allowedColumns as $col) {
                $newRecord[$col] = $record[$col] ?? null;
            }
            // Add timestamps
            $newRecord['created_at'] = now();
            $newRecord['updated_at'] = now();

            $filteredValidRecords[] = $newRecord;
        }

        // Break into chunks to avoid "too many placeholders"
        $chunks = array_chunk($filteredValidRecords, 500);

        // Insert or ignore duplicates (requires a unique index on 'email')
        foreach ($chunks as $chunk) {
            DB::table('hubspot_contacts')->insertOrIgnore($chunk);
        }

        // Generate a CSV with the same column order
        $user = Auth::user();
        $handleTemp = fopen('php://temp', 'r+');

        // Use the $allowedColumns array as your header row
        fputcsv($handleTemp, $allowedColumns);

        // Write each row in the same column order
        foreach ($filteredValidRecords as $record) {
            $rowData = [];
            foreach ($allowedColumns as $col) {
                $rowData[] = $record[$col] ?? '';
            }
            fputcsv($handleTemp, $rowData);
        }

        rewind($handleTemp);
        $csvContent = stream_get_contents($handleTemp);
        fclose($handleTemp);

        $timestamp = now()->format('Y-m-d-H-i-s');
        $cleanFileName = 'synced-file.' . $timestamp . '.csv';

        CSVImport::create([
            'file_name'    => $cleanFileName,
            'file_content' => $csvContent,
            'user_id'      => $user->id ?? null,
        ]);

        // Clear session
        Session::forget('valid_records');
        Session::forget('invalid_records');
        Session::forget('duplicate_records');
        Session::forget('removed_records');
        Session::forget('import_summary');

        return redirect()->back()->with('success', 'Successfully synced valid contacts to the database.');
    }
}
