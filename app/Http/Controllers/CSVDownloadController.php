<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\StreamedResponse;
class CSVDownloadController extends Controller{
    public function downloadCSV(){
        $fileName = 'sample.csv';

        $columns = ['name', 'email', 'contact_number', 'address', 'country','qualification','job_role','company_name','skills','social_profile'];

        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            // Add data rows (for example purposes, this is just dummy data)
            $rows = [
          // Add more rows as needed
            ];

            foreach ($rows as $row) {
                fputcsv($file, $row);
            }

            fclose($file);
        };

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
        ];

        return new StreamedResponse($callback, 200, $headers);
    }
}
