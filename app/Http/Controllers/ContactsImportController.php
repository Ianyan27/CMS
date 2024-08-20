<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\ContactsImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ContactsImportController extends Controller
{
    public function import(Request $request)
    {
        // Validate the uploaded file
        $fileValidator = Validator::make($request->all(), [
            'csv_file' => 'required|mimes:csv,txt|max:2048', // Max 2MB
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

        // Read the CSV file into an array
        $csvData = Excel::toArray([], $file);
        $rows = $csvData[0]; // Get the rows of the CSV file
        $header = array_map('strtolower', array_shift($rows)); // Normalize header to lowercase

        // Define the required logical columns
        $requiredColumns = ['name', 'email', 'contact_number'];

        // Get the column map from the ContactsImport class
        $columnMap = (new ContactsImport)->getColumnMap();

        // Check for missing required columns
        $mappedColumns = array_merge(...array_values($columnMap));
        $missingColumns = array_diff($requiredColumns, array_intersect($header, $mappedColumns));

        if (!empty($missingColumns)) {
            $escapedColumns = array_map('htmlspecialchars', $missingColumns, [ENT_QUOTES, 'UTF-8']);
            $missingColumnsString = implode('", "', $escapedColumns);
            $errorMessage = 'The CSV file must contain the following logical column(s): "' . $missingColumnsString . '".';

            return response()->json([
                'success' => false,
                'message' => $errorMessage,
                'missing_columns' => $missingColumns
            ], 400);
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
                $name = $row[$nameColumn];
                $validationData['name'] = $name;
                $validationRules['name'] = 'required|regex:/^[a-zA-Z\s]+$/';
            }

            if ($emailColumn !== null) {
                $email = $row[$emailColumn];
                $validationData['email'] = $email;
                $validationRules['email'] = 'required|email|unique:contacts,email';
            }

            if ($phoneColumn !== null) {
                $phone = $row[$phoneColumn];
                $validationData['phone'] = $phone;
                $validationRules['phone'] = 'required|regex:/^\+?[0-9]+$/';
            }

            if ($urlColumn !== null) {
                $url = $row[$urlColumn];
                $validationData['url'] = $url;
                $validationRules['url'] = 'nullable|url';
            }

            if ($dateColumn !== null) {
                $date = $row[$dateColumn];
                $validationData['date'] = $date;
                $validationRules['date'] = 'nullable|date_format:Y-m-d';
            }

            // Validate the data
            $validator = Validator::make($validationData, $validationRules);

            if ($validator->fails()) {
                $errors[] = [
                    'row' => $index + 1,
                    'errors' => $validator->errors()
                ];
                $invalidRows[] = $row;
            } else {
                $validRows[] = $row;
            }
        }

        // If there are validation errors, return them in JSON format
        if (!empty($errors)) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed for some rows.',
                'row_errors' => $errors
            ], 422);
        }

        // Handle valid rows
        if (!empty($validRows)) {
            $this->importValidRows($validRows, $header);
        }

        // Handle invalid rows
        if (!empty($invalidRows)) {
            return $this->exportInvalidRows($invalidRows, $header);
        }

        return response()->json([
            'success' => true,
            'message' => 'CSV imported successfully!'
        ]);
    }

    private function getColumnIndex($logicalColumn, $header, $columnMap)
    {
        foreach ($columnMap[$logicalColumn] as $possibleColumn) {
            $index = array_search(strtolower($possibleColumn), $header);
            if ($index !== false) {
                return $index;
            }
        }
        return null;
    }

    private function importValidRows(array $validRows, array $header)
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'valid_csv');
        $handle = fopen($tempFile, 'w+');
        fputcsv($handle, $header);

        foreach ($validRows as $row) {
            fputcsv($handle, $row);
        }

        fclose($handle);

        try {
            Excel::import(new ContactsImport, $tempFile);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while importing data: ' . $e->getMessage()
            ], 500);
        } finally {
            unlink($tempFile);
        }
    }

    private function exportInvalidRows(array $invalidRows, array $header)
    {
        $invalidCsvData = array_merge([$header], $invalidRows);
        $invalidCsvFileName = 'invalid_emails.csv';

        Storage::disk('local')->put($invalidCsvFileName, $this->arrayToCsv($invalidCsvData));

        return response()->json([
            'success' => false,
            'message' => 'Some rows were invalid. You can download the file containing invalid rows.',
            'download_link' => url('storage/' . $invalidCsvFileName)
        ]);
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
