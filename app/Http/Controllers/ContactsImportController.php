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
        $header = array_map('strtolower', array_shift($rows));

        // Define the required logical columns
        $requiredColumns = ['name', 'email', 'contact_number'];
        $columnMap = (new ContactsImport)->getColumnMap();

        // Find missing required columns
        $missingColumns = [];
        foreach ($requiredColumns as $required) {
            if (!array_filter($columnMap[$required] ?? [], fn($col) => in_array(strtolower($col), $header))) {
                $missingColumns[] = $required;
            }
        }

        if (!empty($missingColumns)) {
            return response()->json([
                'success' => false,
                'message' => 'The CSV file must contain the following column(s): ' . implode(', ', $missingColumns)
            ], 422);
        }

        $validRows = [];
        $invalidRows = [];
        $duplicateRows = [];
        $emailColumnIndex = $this->getColumnIndex('email', $header, $columnMap);
        $phoneColumnIndex = $this->getColumnIndex('contact_number', $header, $columnMap);
        $validationErrors = [];

        foreach ($rows as $index => $row) {
            $validationData = [];
            $validationRules = [
                'name' => 'required|regex:/^[a-zA-Z\s]+$/',
                'email' => 'required|email',
                'contact_number' => 'required|regex:/^\+?[0-9]+$/',
            ];

            // Prepare validation data
            $name = trim($row[$this->getColumnIndex('name', $header, $columnMap)]);
            $validationData['name'] = $name;

            $email = trim($row[$emailColumnIndex]);
            $validationData['email'] = $email;

            $contactNumber = trim($row[$phoneColumnIndex]);
            $validationData['contact_number'] = $contactNumber;

            // Validate row
            $validator = Validator::make($validationData, $validationRules);
            if ($validator->fails()) {
                $invalidRows[] = array_merge($row, ['validation_errors' => $validator->errors()->toArray()]);
                $validationErrors[] = $validator->errors()->toArray();
                continue;
            }

            // Check for duplicates
            if (Contact::where('email', $email)->exists()) {
                $duplicateRows[] = $row;
                continue;
            }

            // Add to valid rows
            $validRows[] = $row;
        }

        // Import valid rows to the database
        if (!empty($validRows)) {
            $this->importValidRows($validRows, $header);
        }

        return response()->json([
            'success' => true,
            'message' => 'Import completed.',
            'data' => [
                'valid_count' => count($validRows),
                'invalid_count' => count($invalidRows),
                'duplicate_count' => count($duplicateRows),
                'download_invalid_link' => !empty($invalidRows) ? $this->exportInvalidRows($invalidRows, $header) : null
            ]
        ]);
    }

    private function getColumnIndex($logicalColumn, $header, $columnMap)
    {
        if (isset($columnMap[$logicalColumn])) {
            foreach ($columnMap[$logicalColumn] as $possibleColumn) {
                $index = array_search(strtolower($possibleColumn), $header);
                if ($index !== false) {
                    return $index;
                }
            }
        }
        return null;
    }

    private function importValidRows(array $validRows, array $header)
    {
        foreach ($validRows as $row) {
            $contact = new Contact();
            $contact->name = $row[$this->getColumnIndex('name', $header, (new ContactsImport)->getColumnMap())] ?? '';
            $contact->contact_number = $row[$this->getColumnIndex('contact_number', $header, (new ContactsImport)->getColumnMap())] ?? '';
            $contact->social_profile = $row[$this->getColumnIndex('social_profile', $header, (new ContactsImport)->getColumnMap())] ?? '';

            $email = $row[$this->getColumnIndex('email', $header, (new ContactsImport)->getColumnMap())] ?? null;
            if (!empty($email)) {
                $contact->email = $email;
            }

            $datetimeOfHubspotSync = $row[$this->getColumnIndex('datetime_of_hubspot_sync', $header, (new ContactsImport)->getColumnMap())] ?? null;
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
