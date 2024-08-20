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
            $csvData = Excel::toArray([], $file);
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
            $found = false;
            foreach ($columnMap[$required] ?? [] as $possibleColumn) {
                if (in_array(strtolower($possibleColumn), $header)) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $missingColumns[] = $required;
            }
        }

        if (!empty($missingColumns)) {
            $escapedColumns = array_map(fn($column) => htmlspecialchars($column, ENT_QUOTES, 'UTF-8'), $missingColumns);
            $missingColumnsString = implode('", "', $escapedColumns);
            $errorMessage = 'The CSV file must contain the following logical column(s): "' . $missingColumnsString . '".';
            return redirect()->back()->with('error', $errorMessage);
        }

        // Cache the indices for required columns
        $nameColumn = $this->getColumnIndex('name', $header, $columnMap);
        $emailColumn = $this->getColumnIndex('email', $header, $columnMap);
        $phoneColumn = $this->getColumnIndex('contact_number', $header, $columnMap);
        $urlColumn = $this->getColumnIndex('social_profile', $header, $columnMap);
        $dateColumn = $this->getColumnIndex('datetime_of_hubspot_sync', $header, $columnMap);

        $validRows = [];
        $invalidRows = [];
        $errors = [];

        foreach ($rows as $index => $row) {
            $validationData = [];
            $validationRules = [];

            if ($nameColumn !== null) {
                $name = trim($row[$nameColumn]);
                $validationData['name'] = $name;
                $validationRules['name'] = 'required|regex:/^[a-zA-Z\s]+$/';
            }

            if ($emailColumn !== null) {
                $email = trim($row[$emailColumn]);
                $validationData['email'] = $email;
                $validationRules['email'] = 'email|unique:contacts,email';
            }

            if ($phoneColumn !== null) {
                $phone = trim($row[$phoneColumn]);
                $validationData['contact_number'] = $phone;
                $validationRules['contact_number'] = 'regex:/^\+?[0-9]+$/';
            }

            if ($urlColumn !== null) {
                $url = trim($row[$urlColumn]);
                $validationData['social_profile'] = $url;
                $validationRules['social_profile'] = 'nullable|url';
            }

            if ($dateColumn !== null) {
                $date = trim($row[$dateColumn]);
                $validationData['datetime_of_hubspot_sync'] = $date;
                $validationRules['datetime_of_hubspot_sync'] = 'nullable|date_format:Y-m-d';
            }

            // Validate the data
            $validator = Validator::make($validationData, $validationRules);

            if ($validator->fails()) {
                $errors[] = [
                    'row' => $index + 1,
                    'errors' => $validator->errors()
                ];
                $invalidRows[] = array_merge($row, ['validation_errors' => $validator->errors()->toArray()]);
            } else {
                $validRows[] = $row;
            }
        }

        if (!empty($validRows)) {
            $this->importValidRows($validRows, $header);
        }

        if (!empty($invalidRows)) {
            return $this->exportInvalidRows($invalidRows, $header);
        }

        return response()->json([
            'success' => true,
            'message' => 'CSV imported successfully!',
            'errors' => !empty($errors) ? $errors : null
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
            $nameColumnIndex = $this->getColumnIndex('name', $header, (new ContactsImport)->getColumnMap());
            $emailColumnIndex = $this->getColumnIndex('email', $header, (new ContactsImport)->getColumnMap());
            $contactNumberColumnIndex = $this->getColumnIndex('contact_number', $header, (new ContactsImport)->getColumnMap());
            $socialProfileColumnIndex = $this->getColumnIndex('social_profile', $header, (new ContactsImport)->getColumnMap());
            $datetimeOfHubspotSyncColumnIndex = $this->getColumnIndex('datetime_of_hubspot_sync', $header, (new ContactsImport)->getColumnMap());

            $contact->name = $row[$nameColumnIndex] ?? '';
            $contact->contact_number = $row[$contactNumberColumnIndex] ?? '';
            $contact->social_profile = $row[$socialProfileColumnIndex] ?? '';

            // Handle email
            $email = $row[$emailColumnIndex] ?? null;
            if (empty($email)) {
                // Skip the row if email is empty
                continue;
            } else {
                // Check if the email already exists in the database
                if (Contact::where('email', $email)->exists()) {
                    // Skip or handle the duplicate email case
                    continue;
                }
                $contact->email = $email;
            }

            // Handle datetime_of_hubspot_sync
            $datetimeOfHubspotSync = $row[$datetimeOfHubspotSyncColumnIndex] ?? null;
            $contact->datetime_of_hubspot_sync = !empty($datetimeOfHubspotSync) ? $datetimeOfHubspotSync : null;

            $contact->save();
        }
    }



    private function exportInvalidRows(array $invalidRows, array $header)
    {
        $invalidCsvData = array_merge([$header], $this->flattenInvalidRows($invalidRows));
        $invalidCsvFileName = 'invalid_emails.csv';

        try {
            Storage::disk('local')->put($invalidCsvFileName, $this->arrayToCsv($invalidCsvData));
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while exporting invalid rows: ' . $e->getMessage()
            ], 500);
        }

        return response()->download(storage_path('app/' . $invalidCsvFileName))->deleteFileAfterSend(true);
    }

    private function flattenInvalidRows(array $invalidRows)
    {
        return array_map(function ($row) {
            return array_map(function ($item) {
                // If the item is an array (e.g., validation errors), convert it to a JSON string or serialize it
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
