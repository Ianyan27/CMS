<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\ContactsImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ContactsImportController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt',
        ]);

        $file = $request->file('csv_file');

        // Read the CSV file into an array
        $csvData = Excel::toArray([], $file);
        $rows = $csvData[0]; // Get the rows of the CSV file
        $header = array_shift($rows); // Remove and get the header row

        // Define the required columns (These are logical names, not necessarily in the CSV)
        $requiredColumns = ['name', 'email', 'contact_number'];

        // Map the CSV headers to logical columns using the ContactsImport class
        $columnMap = (new ContactsImport)->getColumnMap();


        // Find missing required columns by checking against the header map
        $missingColumns = [];
        foreach ($requiredColumns as $required) {
            $found = false;
            foreach ($columnMap[$required] ?? [] as $possibleColumn) {
                if (in_array($possibleColumn, $header)) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $missingColumns[] = $required;
            }
        }

        if (!empty($missingColumns)) {
            // Required columns are missing, prepare the error message
            $missingColumnsString = implode('", "', $missingColumns);
            $errorMessage = 'The CSV file must contain the following logical column(s): "' . $missingColumnsString . '".';

            // Show the error message and redirect back to the import page
            return redirect()->back()->with('error', $errorMessage);
        }

        $validRows = [];
        $invalidRows = [];

        foreach ($rows as $row) {
            // Find the email field in the row using the mapped header
            $emailColumn = null;
            foreach ($columnMap['email'] as $possibleColumn) {
                if (in_array($possibleColumn, $header)) {
                    $emailColumn = $possibleColumn;
                    break;
                }
            }

            $email = $row[array_search($emailColumn, $header)];

            // Validate the email format
            $validator = Validator::make(['email' => $email], [
                'email' => 'required|email',
            ]);

            if ($validator->fails()) {
                // Store the invalid row for exporting later
                $invalidRows[] = $row;
            } else {
                // Store the valid row for importing
                $validRows[] = $row;
            }
        }

        // If there are valid rows, import them using ContactsImport
        if (!empty($validRows)) {
            $tempFile = tempnam(sys_get_temp_dir(), 'valid_csv');
            $handle = fopen($tempFile, 'w+');
            fputcsv($handle, $header);

            foreach ($validRows as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);

            // Import the valid CSV file
            Excel::import(new ContactsImport, $tempFile);

            // Remove the temporary file
            unlink($tempFile);
        }

        // Handle exporting invalid rows as CSV
        if (!empty($invalidRows)) {
            $invalidCsvData = array_merge([$header], $invalidRows);
            $invalidCsvFileName = 'invalid_emails.csv';

            // Store invalid CSV temporarily
            Storage::disk('local')->put($invalidCsvFileName, $this->arrayToCsv($invalidCsvData));

            // Return download link for invalid rows
            return response()->download(storage_path('app/' . $invalidCsvFileName))->deleteFileAfterSend(true);
        }

        return redirect('/contacts')->with('success', 'CSV imported successfully!');
    }

    private function arrayToCsv(array $array)
    {
        $csv = '';
        foreach ($array as $row) {
            $csv .= implode(',', $row) . "\n";
        }
        return $csv;
    }
}
