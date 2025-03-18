<?php
// exportContacts.php

// Bootstrap Laravel if this file is outside of Laravel's routing system.
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Boot up the kernel to have access to DB facade and other services.
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;

// Define the file path where the CSV will be stored.
$csvPath = storage_path('app/csv/hubspot_contacts.csv');

// Ensure the directory exists
if (!file_exists(dirname($csvPath))) {
    mkdir(dirname($csvPath), 0777, true);
}

// Open the file for writing CSV data.
$file = fopen($csvPath, 'w');

// Write CSV header row.
fputcsv($file, [
    'hubspot_id',
    'email',
    'firstname',
    'lastname',
    'gender',
    'hubspot_created_at',
    'hubspot_updated_at',
    'phone',
    'hubspot_owner_id',
    'hs_lead_status',
    'company',
    'lifecyclestage',
    'country',
    'created_at',
    'updated_at'
]);

// Retrieve records from the database.
$records = DB::table('hubspot_contacts')->get();

foreach ($records as $record) {
    fputcsv($file, [
        $record->hubspot_id,
        $record->email,
        $record->firstname,
        $record->lastname,
        $record->gender,
        $record->hubspot_created_at,
        $record->hubspot_updated_at,
        $record->phone,
        $record->hubspot_owner_id,
        $record->hs_lead_status,
        $record->company,
        $record->lifecyclestage,
        $record->country,
        $record->created_at,
        $record->updated_at,
    ]);
}

fclose($file);

// Now, send headers and output the CSV file to force a download in the browser.
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=hubspot_contacts.csv');
readfile($csvPath);
exit;
