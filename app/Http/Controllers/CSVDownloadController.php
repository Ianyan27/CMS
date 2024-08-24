<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\StreamedResponse;
class CSVDownloadController extends Controller
{
    public function downloadCSV()
    {
        $fileName = 'sample.csv';

        $columns = ['name', 'email', 'contact_number', 'address', 'country','qualification','job_role','company_name','skills','social_profile','datetime_of__hubspot_sync'];

        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            // Add data rows (for example purposes, this is just dummy data)
            $rows = [
                ['John Doe', 'john.doe@example.com', '123-456-7890', '123 Main St', 'USA', 'B.Sc', 'Software Engineer', 'Tech Corp', 'PHP, Laravel, JavaScript', 'linkedin.com/in/johndoe', '2024-08-24 10:30:00'],
                ['Jane Smith', 'jane.smith@example.com', '098-765-4321', '456 Elm St', 'Canada', 'M.Sc', 'Data Analyst', 'Data Inc', 'Python, SQL, R', 'linkedin.com/in/janesmith', '2024-08-24 11:00:00'],
                ['Alice Johnson', 'alice.johnson@example.com', '555-123-4567', '789 Oak St', 'UK', 'MBA', 'Product Manager', 'Biz Group', 'Leadership, Marketing, Agile', 'linkedin.com/in/alicejohnson', '2024-08-24 12:15:00'],
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
