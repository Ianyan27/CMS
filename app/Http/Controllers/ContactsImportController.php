<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\ContactsImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\Contact;

class ContactsImportController extends Controller
{
    public function import(Request $request)
    {
        // Validate the uploaded file
        $fileValidator = Validator::make($request->all(), [
            'csv_file' => 'required|mimes:csv,txt|max:2048',
        ], [
            'csv_file.required' => 'The CSV file is required.',
            'csv_file.mimes' => 'The uploaded file must be a file of type: csv, txt.',
            'csv_file.max' => 'The uploaded file may not be greater than 2MB.',
        ]);

        if ($fileValidator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed for the uploaded file.',
                'errors' => $fileValidator->errors()
            ], 422);
        }

        $file = $request->file('csv_file');

        try {
            // Read the CSV file into an array
            $csvData = Excel::toArray(new ContactsImport, $file);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to read the CSV file: ' . $e->getMessage()
            ], 500);
        }

        $rows = $csvData[0];
        $header = array_map('strtolower', array_keys($rows[0])); // Normalize header

        // Define the required logical columns
        $requiredColumns = ['name', 'email', 'contact_number'];

        // Get the column map from the ContactsImport class
        $columnMap = (new ContactsImport)->getColumnMap();
        \Log::info('Column Map:', ['column_map' => $columnMap]);
        \Log::info('Header:', ['header' => $header]);

        // Map CSV header to logical columns
        $mappedHeader = [];
        foreach ($columnMap as $logicalColumn => $possibleColumns) {
            foreach ($possibleColumns as $possibleColumn) {
                if (in_array(strtolower($possibleColumn), $header)) {
                    $mappedHeader[$logicalColumn] = array_search(strtolower($possibleColumn), $header);
                    break;
                }
            }
        }

        \Log::info('Mapped Header:', ['mapped_header' => $mappedHeader]);

        // Check for missing required columns
        $missingColumns = array_diff($requiredColumns, array_keys($mappedHeader));

        if (!empty($missingColumns)) {
            return response()->json([
                'success' => false,
                'message' => 'The CSV file must contain the following column(s): ' . implode(', ', $missingColumns)
            ], 422);
        }

        $validRows = [];
        $invalidRows = [];
        $duplicateRows = [];
        $validationErrors = [];

        foreach ($rows as $index => $row) {

            $name = trim($row[$columnMap['name'][0] ?? ''] ?? '');
            $email = trim($row[$columnMap['email'][0] ?? ''] ?? '');
            $contactNumber = trim($row[$columnMap['contact_number'][0] ?? ''] ?? '');

            // Check for duplicates
            if (Contact::where('email', $email)->exists()) {
                $duplicateRows[] = $row;
                continue;
            }

            $validationData = [
                'name' => $name,
                'email' => $email,
                'contact_number' => $contactNumber,
            ];

            $validationRules = [
                'name' => 'required',
                'email' => 'nulllable|email',
                'contact_number' => 'nullable|numeric',
            ];

            $validator = Validator::make($validationData, $validationRules);
            if ($validator->fails()) {
                $invalidRows[] = array_merge($row, ['validation_errors' => $validator->errors()->toArray()]);
                $validationErrors[] = $validator->errors()->toArray();

                continue;
            }

            $validRows[] = $row;
        }

        // Import valid rows to the database
        if (!empty($validRows)) {
            $this->importValidRows($validRows, $columnMap);
        }

        // Export invalid rows
        if (!empty($invalidRows)) {
            $invalidCsvData = array_merge([$header], $this->flattenInvalidRows($invalidRows));
            $invalidCsvFileName = 'invalid_rows.csv';

            try {
                Storage::disk('local')->put($invalidCsvFileName, $this->arrayToCsv($invalidCsvData));
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to export invalid rows: ' . $e->getMessage()
                ], 500);
            }

            $invalidCsvUrl = Storage::url($invalidCsvFileName);

            return response()->json([
                'success' => true,
                'message' => 'Import completed.',
                'data' => [
                    'valid_count' => count($validRows),
                    'invalid_count' => count($invalidRows),
                    'duplicate_count' => count($duplicateRows),
                    'download_invalid_link' => $invalidCsvUrl
                ]
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Import completed.',
            'data' => [
                'valid_count' => count($validRows),
                'invalid_count' => 0,
                'duplicate_count' => count($duplicateRows)
            ]
        ]);
    }

    private function importValidRows(array $validRows, array $mappedHeader)
    {
        foreach ($validRows as $row) {
            \Log::info('Importing Row:', ['row_data' => $row]);
            $contact = new Contact();
            $contact->name = $row[$mappedHeader['name']] ?? '';
            $contact->contact_number = $row[$mappedHeader['contact_number']] ?? '';
            $contact->social_profile = $row[$mappedHeader['social_profile']] ?? '';

            $email = $row[$mappedHeader['email']] ?? null;
            if (!empty($email)) {
                $contact->email = $email;
            }

            $datetimeOfHubspotSync = $row[$mappedHeader['datetime_of_hubspot_sync']] ?? null;
            $contact->datetime_of_hubspot_sync = !empty($datetimeOfHubspotSync) ? $datetimeOfHubspotSync : null;

            $contact->save();
        }
    }

    private function exportInvalidRows(array $invalidRows, array $header)
    {
        $invalidCsvData = array_merge([$header], $this->flattenInvalidRows($invalidRows));
        $invalidCsvFileName = 'invalid_rows.csv';

        try {
            Storage::disk('local')->put($invalidCsvFileName, $this->arrayToCsv($invalidCsvData));
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to export invalid rows: ' . $e->getMessage()
            ];
        }

        return Storage::url($invalidCsvFileName);
    }

    private function flattenInvalidRows(array $invalidRows)
    {
        return array_map(function ($row) {
            return array_map(function ($item) {
                return is_array($item) ? json_encode($item) : $item;
            }, $row);
        }, $invalidRows);
    }

    private function arrayToCsv(array $array)
    {
        $csv = fopen('php://temp', 'r+');
        foreach ($array as $row) {
            fputcsv($csv, $row);
        }
        rewind($csv);
        return stream_get_contents($csv);
    }
}
