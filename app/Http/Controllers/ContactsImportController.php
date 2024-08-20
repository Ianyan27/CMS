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
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt|max:2048', // Max 2MB
        ], [
            'csv_file.mimes' => 'The uploaded file must be a file of type: csv, txt.',
        ]);

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

            return redirect()->back()->with('error', $errorMessage);
        }

        // Cache the index of the email column for efficiency
        $emailColumn = $this->getColumnIndex('email', $header, $columnMap);
        $validRows = [];
        $invalidRows = [];

        foreach ($rows as $row) {
            if ($emailColumn !== null) {
                $email = $row[$emailColumn];

                // Validate the email format
                $validator = Validator::make(['email' => $email], [
                    'email' => 'required|email',
                ]);

                if ($validator->fails() || $this->isDuplicateEmail($email)) {
                    $invalidRows[] = $row;
                } else {
                    $validRows[] = $row;
                }
            } else {
                $invalidRows[] = $row; // Add to invalid rows if email column is missing
            }
        }

        // Handle valid rows
        if (!empty($validRows)) {
            $this->importValidRows($validRows, $header);
        }

        // Handle invalid rows
        if (!empty($invalidRows)) {
            return $this->exportInvalidRows($invalidRows, $header);
        }

        return redirect('/contact-listing')->with('success', 'CSV imported successfully!');
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

    private function isDuplicateEmail($email)
    {
        return DB::table('contacts')->where('email', $email)->exists();
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
            return redirect()->back()->with('error', 'An error occurred while importing data: ' . $e->getMessage());
        } finally {
            unlink($tempFile);
        }
    }

    private function exportInvalidRows(array $invalidRows, array $header)
    {
        $invalidCsvData = array_merge([$header], $invalidRows);
        $invalidCsvFileName = 'invalid_emails.csv';

        Storage::disk('local')->put($invalidCsvFileName, $this->arrayToCsv($invalidCsvData));

        return response()->download(storage_path('app/' . $invalidCsvFileName))->deleteFileAfterSend(true);
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
