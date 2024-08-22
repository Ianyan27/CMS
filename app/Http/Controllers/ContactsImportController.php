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
            // Import the data into the database using the ContactsImport class
            $import = new ContactsImport;
            Excel::import($import, $file);
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
            $invalidCsvData = array_merge([['name', 'email', 'contact_number', 'validation_errors']], $invalidRows);
            $invalidCsvFileName = 'invalid_rows.csv';
            $invalidCsvUrl = $this->exportCsv($invalidCsvFileName, $invalidCsvData);
            $fileLinks['invalid_rows'] = $invalidCsvUrl;
        }

        if (!empty($duplicateRows)) {
            $duplicateCsvData = array_merge([['name', 'email', 'contact_number']], $duplicateRows);
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
            ]
        ]);
    }

    private function exportCsv($fileName, $data)
    {
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

    private function arrayToCsv(array $array)
    {
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
}
