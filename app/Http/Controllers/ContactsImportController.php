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
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt',
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

        // Map the CSV headers to logical columns using the ContactsImport class
        $columnMap = (new ContactsImport)->getColumnMap();

        // Find missing required columns by checking against the header map
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
            // Escape special characters in the missing columns
            $escapedColumns = array_map(function($column) {
                return htmlspecialchars($column, ENT_QUOTES, 'UTF-8');
            }, $missingColumns);

            // Join the escaped column names with proper formatting
            $missingColumnsString = implode('", "', $escapedColumns);
            $errorMessage = 'The CSV file must contain the following logical column(s): "' . $missingColumnsString . '".';

            // Show the error message and redirect back to the import page
            return redirect()->back()->with('error', $errorMessage);
        }

        $validRows = [];
        $invalidRows = [];

        foreach ($rows as $row) {
            // Validate and map each required column
            $emailColumn = null;
            foreach ($columnMap['email'] as $possibleColumn) {
                if (in_array(strtolower($possibleColumn), $header)) {
                    $emailColumn = $possibleColumn;
                    break;
                }
            }

            if ($emailColumn !== null) {
                $email = $row[array_search(strtolower($emailColumn), $header)];

                // Validate the email format
                $validator = Validator::make(['email' => $email], [
                    'email' => 'required|email',
                ]);

                if ($validator->fails()) {
                    // Store the invalid row for exporting later
                    $invalidRows[] = $row;
                } else {
                    // Check for duplicates in the database
                    $existingRecord = DB::table('contacts')->where('email', $email)->first();

                    if ($existingRecord) {
                        // Handle the duplicate record
                        $invalidRows[] = $row;
                    } else {
                        // Store the valid row for importing
                        $validRows[] = $row;
                    }
                }
            } else {
                $invalidRows[] = $row; // Add to invalid rows if email column is missing
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

            try {
                // Import the valid CSV file
                Excel::import(new ContactsImport, $tempFile);
            } catch (\Illuminate\Database\QueryException $e) {
                // Handle database errors, such as unique constraint violations
                return redirect()->back()->with('error', 'An error occurred while importing data: ' . $e->getMessage());
            } finally {
                // Remove the temporary file
                unlink($tempFile);
            }
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

        return redirect('/contact-listing')->with('success', 'CSV imported successfully!');
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
