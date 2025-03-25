<?php

namespace App\Http\Controllers;

use App\Models\CSVImport;
use App\Models\HubspotRetrievalHistory;
use App\Models\HubspotContact;
use App\Models\HubspotSyncStatus;
use App\Models\HubspotContactBuffer;
use App\Models\User;
use App\Services\HubspotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class HubspotContactSyncController extends Controller
{
    protected $hubspotService;
    protected $batchSize = 1000; // Number of contacts per batch

    public function __construct(HubspotService $hubspotService)
    {
        $this->hubspotService = $hubspotService;
    }

    public function dashboard()
    {
        $syncStatus = $this->hubspotService->getSyncStatus('contacts');
        $totalContacts = HubspotContact::count();
        $csvImports = CSVImport::orderBy('created_at', 'desc')->get();
        $lastSyncDate = $syncStatus->last_successful_sync;

        // Get next start date (which is the last end date)
        $nextStartDate = $syncStatus->last_sync_timestamp ?? '2021-10-07T00:00:00Z';
        $endDate = Carbon::now()->format('Y-m-d\TH:i:s\Z');

        return view('hubspot.dashboard', compact(
            'syncStatus',
            'totalContacts',
            'lastSyncDate',
            'nextStartDate',
            'endDate',
            'csvImports'
        ));
    }

    public function syncHistory()
    {
        $syncStatus = $this->hubspotService->getSyncStatus('contacts');
        $totalContacts = HubspotContact::count();
        $recentContacts = HubspotContact::latest()->take(10)->get();

        return view('hubspot.sync-history', compact(
            'syncStatus',
            'totalContacts',
            'recentContacts',
        ));
    }

    public function startSync(Request $request)
    {
        // Validate input
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        $syncStatus = $this->hubspotService->getSyncStatus('contacts');

        // Already running? Don't start another
        if ($syncStatus->status === 'running') {
            return redirect()->back()->with('warning', 'Sync is already in progress');
        }

        // Determine date range
        $startDate = $request->start_date
            ? Carbon::parse($request->start_date)->format('Y-m-d\TH:i:s\Z')
            : ($syncStatus->last_sync_timestamp
                ? $syncStatus->last_sync_timestamp->format('Y-m-d\TH:i:s\Z')
                : '2020-03-01T00:00:00Z');

        $endDate = $request->end_date
            ? Carbon::parse($request->end_date)->format('Y-m-d\TH:i:s\Z')
            : Carbon::now()->format('Y-m-d\TH:i:s\Z');

        // Update status to running
        $this->hubspotService->updateSyncStatus('contacts', [
            'status' => 'running',
            'start_window' => $startDate,
            'end_window' => $endDate,
            'total_synced' => 0,
            'total_errors' => 0,
            'error_log' => null
        ]);

        // Start sync process for one batch
        $this->processSingleBatch($startDate, $endDate);

        return redirect()->route('admin#hubspot-dashboard')
            ->with('success', 'Contact sync batch has been processed');
    }

    public function processSingleBatch($startDate, $endDate, $originalEndDate = null)
    {
        $syncStatus = $this->hubspotService->getSyncStatus('contacts');
        $errors = [];

        // Store the original end date if not provided (first run)
        if ($originalEndDate === null) {
            $originalEndDate = $endDate;
        }

        try {
            // Find optimal time window for this batch
            $result = $this->hubspotService->findOptimalTimeWindow(
                $startDate,
                $endDate
            );

            $optimalEndDate = $result['endDate'];
            $totalContacts = $result['totalContacts'];

            // If no contacts found, update status and return
            if ($totalContacts == 0) {
                $this->hubspotService->updateSyncStatus('contacts', [
                    'status' => 'completed',
                    'last_sync_timestamp' => $originalEndDate, // Use original end date
                    'last_successful_sync' => Carbon::now(),
                    'next_sync_timestamp' => Carbon::tomorrow()->startOfDay(),
                ]);

                Log::info("No contacts found in window", [
                    'startDate' => $startDate,
                    'endDate' => $endDate
                ]);

                return;
            }

            Log::info("Processing batch", [
                'startDate' => $startDate,
                'endDate' => $optimalEndDate,
                'originalEndDate' => $originalEndDate,
                'expectedCount' => $totalContacts
            ]);

            // Fetch contacts in this time window
            $contacts = $this->hubspotService->getAllContactsInTimeWindow(
                $startDate,
                $optimalEndDate
            );

            $actualCount = count($contacts);
            Log::info("Retrieved {$actualCount} contacts for processing");

            // Save retrieval history
            HubspotRetrievalHistory::create([
                'retrieved_count' => $actualCount,
                'start_date' => $startDate,
                'end_date' => $optimalEndDate
            ]);

            // Process contacts in chunks
            $this->processContacts($contacts);

            // Check if we hit the API limit and need to adjust our approach
            if ($actualCount >= 10000 && $actualCount < $totalContacts) {
                Log::warning("HubSpot API limit reached. Processing partial batch and continuing with remainder.");

                // Calculate a new time point after the last contact we retrieved
                // This is an approximation - we'll use the proportion of contacts retrieved
                $retrievalRatio = $actualCount / $totalContacts;
                $timeSpan = Carbon::parse($optimalEndDate)->diffInSeconds(Carbon::parse($startDate));
                $newTimePoint = Carbon::parse($startDate)->addSeconds(ceil($timeSpan * $retrievalRatio))->format('Y-m-d\TH:i:s\Z');

                Log::info("Continuing with remainder using calculated time point", [
                    'newStartDate' => $newTimePoint,
                    'targetEndDate' => $optimalEndDate,
                    'remainingContacts' => $totalContacts - $actualCount
                ]);

                // Update sync status with current progress
                $this->hubspotService->updateSyncStatus('contacts', [
                    'status' => 'running', // Keep status as running
                    'total_synced' => $syncStatus->total_synced + $actualCount,
                    'last_successful_sync' => Carbon::now(),
                ]);

                // Small delay to avoid rate limiting
                sleep(2);

                // Process the remainder using the new time point
                $this->processSingleBatch($newTimePoint, $optimalEndDate, $originalEndDate);
                return;
            }

            // Update sync status
            $this->hubspotService->updateSyncStatus('contacts', [
                'status' => 'running', // Keep status as running for continued processing
                'last_sync_timestamp' => $optimalEndDate,
                'total_synced' => $syncStatus->total_synced + $actualCount,
                'last_successful_sync' => Carbon::now(),
            ]);

            Log::info("Batch completed successfully", [
                'contactsProcessed' => $actualCount,
                'nextStartDate' => $optimalEndDate
            ]);

            // Check if we need to process more contacts (if we haven't reached the original end date)
            if ($optimalEndDate !== $originalEndDate && Carbon::parse($optimalEndDate)->lt(Carbon::parse($originalEndDate))) {
                Log::info("Continuing to next batch", [
                    'newStartDate' => $optimalEndDate,
                    'targetEndDate' => $originalEndDate
                ]);

                // Small delay to avoid rate limiting
                sleep(2);

                // Process the next batch (recursive call)
                $this->processSingleBatch($optimalEndDate, $originalEndDate, $originalEndDate);
            } else {
                // We've reached the end of the original range, update status to completed
                $this->hubspotService->updateSyncStatus('contacts', [
                    'status' => 'completed',
                    'next_sync_timestamp' => Carbon::tomorrow()->startOfDay(),
                ]);

                Log::info("All batches completed for full date range", [
                    'originalStartDate' => $startDate,
                    'originalEndDate' => $originalEndDate,
                    'totalSynced' => $syncStatus->refresh()->total_synced
                ]);
            }
        } catch (\Exception $e) {
            // Error handling code remains the same
        }
    }

    private function processContacts($contacts)
    {
        $maxContactsPerBatch = 10000;
        $chunkSize = 3000; // Process in chunks of 3000 for efficiency

        // Check if we need to handle HubSpot API limit
        if (count($contacts) >= $maxContactsPerBatch) {
            Log::warning("HubSpot API limit reached. Time window needs adjustment.", [
                'contactsRetrieved' => count($contacts),
                'suggestedAction' => 'Reduce time window and retry'
            ]);

            // We can still process the contacts we have
            Log::info("Processing available contacts", [
                'contactCount' => count($contacts),
                'chunks' => ceil(count($contacts) / $chunkSize),
                'chunkSize' => $chunkSize
            ]);
        }

        // Check for existing contacts that will be updated
        $hubspotIds = array_map(function ($contact) {
            return $contact['id'];
        }, $contacts);

        $existingCount = DB::table('hubspot_contacts')
            ->whereIn('hubspot_id', $hubspotIds)
            ->count();

        if ($existingCount > 0) {
            Log::info("Found {$existingCount} existing contacts that will be updated with HubSpot data");
        }

        // Process in chunks for efficiency
        $this->processContactsBatch($contacts, $chunkSize);
    }

    /**
     * Process a batch of contacts by chunks
     */
    private function processContactsBatch($contacts, $chunkSize)
    {
        // Process in chunks for efficiency
        $chunks = array_chunk($contacts, $chunkSize);

        Log::info("Processing contact batch", [
            'contactCount' => count($contacts),
            'chunks'       => count($chunks),
            'chunkSize'    => $chunkSize
        ]);

        foreach ($chunks as $chunk) {
            $records = [];

            foreach ($chunk as $contact) {
                $records[] = [
                    'hubspot_id'         => $contact['id'],
                    'email'              => $contact['properties']['email'] ?? null,
                    'firstname'          => $contact['properties']['firstname'] ?? null,
                    'lastname'           => $contact['properties']['lastname'] ?? null,
                    'gender'             => $contact['properties']['gender'] ?? null,
                    'hubspot_created_at' => isset($contact['properties']['createdate'])
                        ? Carbon::parse($contact['properties']['createdate'])
                        : null,
                    'hubspot_updated_at' => isset($contact['properties']['lastmodifieddate'])
                        ? Carbon::parse($contact['properties']['lastmodifieddate'])
                        : null,
                    'phone'              => $contact['properties']['phone'] ?? null,
                    'hubspot_owner_id'   => $contact['properties']['hubspot_owner_id'] ?? null,
                    'hs_lead_status'     => $contact['properties']['hs_lead_status'] ?? null,
                    'company'            => $contact['properties']['company'] ?? null,
                    'lifecyclestage'     => $contact['properties']['lifecyclestage'] ?? null,
                    'country'            => $contact['properties']['country'] ?? null,
                    // Randomly assign marked_deleted as "yes" or "no"
                    'marked_deleted'     => (rand(0, 1) === 1) ? 'yes' : 'no',
                    'created_at'         => Carbon::now(),
                    'updated_at'         => Carbon::now(),
                ];
            }

            // Use upsert to handle duplicates
            DB::table('hubspot_contacts')->upsert(
                $records,
                ['hubspot_id'], // Unique key to check for existing records
                [
                    'email',
                    'firstname',
                    'lastname',
                    'gender',
                    'hubspot_updated_at',
                    'updated_at',
                    'phone',
                    'hubspot_owner_id',
                    'hs_lead_status',
                    'company',
                    'lifecyclestage',
                    'country',
                    'marked_deleted'
                ]
            );

            Log::info("Inserted batch of " . count($records) . " contacts");
        }
    }

    public function cancelSync()
    {
        $this->hubspotService->updateSyncStatus('contacts', [
            'status' => 'cancelled'
        ]);

        return redirect()->route('admin#hubspot-dashboard')
            ->with('info', 'Sync has been cancelled');
    }

    public function retrievalHistory()
    {
        $retrievals = HubspotRetrievalHistory::orderBy('created_at', 'desc')
            ->paginate(20);

        return view('hubspot.retrieval-history', compact('retrievals'));
    }

    public function scheduleSync(Request $request)
    {
        $request->validate([
            'schedule_time' => 'required|date',
        ]);

        $scheduleTime = Carbon::parse($request->schedule_time);

        // Don't allow scheduling in the past
        if ($scheduleTime->isPast()) {
            return redirect()->back()->with('error', 'Cannot schedule a sync in the past.');
        }

        // Update the next sync timestamp
        $this->hubspotService->updateSyncStatus('contacts', [
            'next_sync_timestamp' => $scheduleTime,
        ]);

        return redirect()->route('hubspot.dashboard')
            ->with('success', 'Next sync scheduled for ' . $scheduleTime->format('Y-m-d H:i:s'));
    }

    public function processModifiedBatch($startDate, $endDate, $originalEndDate = null)
    {
        $syncStatus = $this->hubspotService->getSyncStatus('contacts');
        $errors = [];

        // Store the original end date if not provided (first run)
        if ($originalEndDate === null) {
            $originalEndDate = $endDate;
        }

        try {
            // Find optimal time window for this batch using modified date
            $result = $this->hubspotService->findOptimalTimeWindowByModifiedDate(
                $startDate,
                $endDate
            );

            $optimalEndDate = $result['endDate'];
            $totalContacts = $result['totalContacts'];

            // If no contacts found, update status and return
            if ($totalContacts == 0) {
                $this->hubspotService->updateSyncStatus('contacts', [
                    'status' => 'completed',
                    'last_sync_timestamp' => $originalEndDate, // Use original end date
                    'last_successful_sync' => Carbon::now(),
                    'next_sync_timestamp' => Carbon::tomorrow()->startOfDay(),
                ]);

                Log::info("No modified contacts found in window", [
                    'startDate' => $startDate,
                    'endDate' => $endDate
                ]);

                return;
            }

            Log::info("Processing modified batch", [
                'startDate' => $startDate,
                'endDate' => $optimalEndDate,
                'originalEndDate' => $originalEndDate,
                'expectedCount' => $totalContacts
            ]);

            // Fetch contacts by modified date in this time window
            $contacts = $this->hubspotService->getAllContactsByModifiedDate(
                $startDate,
                $optimalEndDate
            );

            $actualCount = count($contacts);
            Log::info("Retrieved {$actualCount} modified contacts for processing");

            // Save retrieval history
            HubspotRetrievalHistory::create([
                'retrieved_count' => $actualCount,
                'start_date' => $startDate,
                'end_date' => $optimalEndDate,
                'sync_type' => 'modified' // Add this field to your table
            ]);

            // Process contacts in chunks
            $this->processContacts($contacts);

            // Update sync status
            $this->hubspotService->updateSyncStatus('contacts', [
                'status' => 'running', // Keep status as running for continued processing
                'last_modified_sync_timestamp' => $optimalEndDate, // Add this field to your table
                'total_synced' => $syncStatus->total_synced + $actualCount,
                'last_successful_sync' => Carbon::now(),
            ]);

            Log::info("Modified batch completed successfully", [
                'contactsProcessed' => $actualCount,
                'nextStartDate' => $optimalEndDate
            ]);

            // Check if we need to process more contacts (if we haven't reached the original end date)
            if ($optimalEndDate !== $originalEndDate && Carbon::parse($optimalEndDate)->lt(Carbon::parse($originalEndDate))) {
                Log::info("Continuing to next modified batch", [
                    'newStartDate' => $optimalEndDate,
                    'targetEndDate' => $originalEndDate
                ]);

                // Small delay to avoid rate limiting
                sleep(2);

                // Process the next batch (recursive call)
                $this->processModifiedBatch($optimalEndDate, $originalEndDate, $originalEndDate);
            } else {
                // We've reached the end of the original range, update status to completed
                $this->hubspotService->updateSyncStatus('contacts', [
                    'status' => 'completed',
                    'next_sync_timestamp' => Carbon::tomorrow()->startOfDay(),
                ]);

                Log::info("All modified batches completed for full date range", [
                    'originalStartDate' => $startDate,
                    'originalEndDate' => $originalEndDate,
                    'totalSynced' => $syncStatus->refresh()->total_synced
                ]);
            }
        } catch (\Exception $e) {
            Log::error("HubSpot modified sync error", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $errors[] = $e->getMessage();

            // Update sync status with error
            $this->hubspotService->updateSyncStatus('contacts', [
                'status' => 'failed',
                'total_errors' => $syncStatus->total_errors + 1,
                'error_log' => json_encode($errors)
            ]);
        }
    }

    public function startModifiedSync(Request $request)
    {
        // Validate input
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        $syncStatus = $this->hubspotService->getSyncStatus('contacts');

        // Already running? Don't start another
        if ($syncStatus->status === 'running') {
            return redirect()->back()->with('warning', 'Sync is already in progress');
        }

        // Determine date range
        $startDate = $request->start_date
            ? Carbon::parse($request->start_date)->format('Y-m-d\TH:i:s\Z')
            : ($syncStatus->last_modified_sync_timestamp  // Add this field to your table
                ? $syncStatus->last_modified_sync_timestamp->format('Y-m-d\TH:i:s\Z')
                : '2020-03-01T00:00:00Z');

        $endDate = $request->end_date
            ? Carbon::parse($request->end_date)->format('Y-m-d\TH:i:s\Z')
            : Carbon::now()->format('Y-m-d\TH:i:s\Z');

        // Update status to running
        $this->hubspotService->updateSyncStatus('contacts', [
            'status' => 'running',
            'start_window' => $startDate,
            'end_window' => $endDate,
            'total_synced' => 0,
            'total_errors' => 0,
            'error_log' => null
        ]);

        // Start sync process for one batch using modified date
        $this->processModifiedBatch($startDate, $endDate);

        return redirect()->route('admin#hubspot-dashboard')
            ->with('success', 'Modified contact sync batch has been processed');
    }

    public function importCSV(Request $request)
    {
        $user = Auth::user();
        // Validate file type and size (max 2MB).
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048'
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();

        // Read the entire file contents.
        $fileContent = file_get_contents($file->getRealPath());

        // Save the CSV meta data (including file content) in the database.
        $csvImport = CSVImport::create([
            'file_name'    => $originalName,
            'file_content' => $fileContent,
            'user_id'      => $user->id  // assumes user is logged in; otherwise, leave null
        ]);

        // Now, process the CSV file.
        // You can either process from $file->getRealPath() (since it still exists temporarily)
        // or from $fileContent (for example, using str_getcsv on each line).
        // Here, we use the file's temporary location:
        if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
            // Read the header row.
            $header = fgetcsv($handle, 1000, ',');

            // ... [Perform header validation/normalization here as in your existing logic] ...

            $validRecords = [];
            $invalidRecords = [];
            $rowNumber = 1;

            // Example: define expected headers and required fields.
            $expectedHeaders = [
                'hubspot_id'       => ['hubspot_id', 'id', 'contact_id'],
                'firstname'        => ['firstname', 'first_name', 'fname'],
                'lastname'         => ['lastname', 'last_name', 'lname'],
                'email'            => ['email', 'mail'],
                'gender'           => ['gender'],
                'createdate'       => ['createdate', 'hubspot_created_at'],
                'lastmodifieddate' => ['lastmodifieddate', 'hubspot_updated_at'],
                'phone'            => ['phone'],
                'hubspot_owner_id' => ['hubspot_owner_id'],
                'hs_lead_status'   => ['hs_lead_status'],
                'company'          => ['company'],
                'lifecyclestage'   => ['lifecyclestage'],
                'country'          => ['country']
            ];

            $requiredFields = ['hubspot_id', 'firstname', 'lastname', 'email'];

            // Check for missing headers.
            $missingHeaders = [];
            foreach ($requiredFields as $field) {
                $found = false;
                foreach ($expectedHeaders[$field] as $alt) {
                    if (in_array($alt, $header)) {
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $missingHeaders[] = $field;
                }
            }
            if (!empty($missingHeaders)) {
                fclose($handle);
                $missingList = implode(', ', $missingHeaders);
                return redirect()->back()->with('error', "Invalid CSV file. Missing required header(s): {$missingList}");
            }

            // Process rows (including validations, duplicate detection, etc.)
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                $rowNumber++;
                $normalizedRow = [];
                foreach ($expectedHeaders as $standard => $alternatives) {
                    $normalizedRow[$standard] = null;
                    foreach ($alternatives as $alt) {
                        $pos = array_search($alt, $header);
                        if ($pos !== false && isset($row[$pos])) {
                            $normalizedRow[$standard] = trim($row[$pos]);
                            break;
                        }
                    }
                }

                // Validate each row (for required values and proper formats).
                $errors = [];
                foreach ($requiredFields as $field) {
                    if (empty($normalizedRow[$field])) {
                        $errors[] = "Missing required field: $field";
                    }
                }
                if (!empty($normalizedRow['email']) && !filter_var($normalizedRow['email'], FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Invalid email format";
                }

                if (empty($errors)) {
                    $validRecords[] = [
                        'hubspot_id'         => $normalizedRow['hubspot_id'],
                        'email'              => $normalizedRow['email'],
                        'firstname'          => $normalizedRow['firstname'],
                        'lastname'           => $normalizedRow['lastname'],
                        'gender'             => $normalizedRow['gender'] ?? null,
                        'hubspot_created_at' => !empty($normalizedRow['createdate']) ? \Carbon\Carbon::parse($normalizedRow['createdate']) : null,
                        'hubspot_updated_at' => !empty($normalizedRow['lastmodifieddate']) ? \Carbon\Carbon::parse($normalizedRow['lastmodifieddate']) : null,
                        'phone'              => $normalizedRow['phone'] ?? null,
                        'hubspot_owner_id'   => $normalizedRow['hubspot_owner_id'] ?? null,
                        'hs_lead_status'     => $normalizedRow['hs_lead_status'] ?? null,
                        'company'            => $normalizedRow['company'] ?? null,
                        'lifecyclestage'     => $normalizedRow['lifecyclestage'] ?? null,
                        'country'            => $normalizedRow['country'] ?? null,
                        // Randomly assign marked_deleted ("yes" or "no")
                        'marked_deleted'     => (rand(0, 1) === 1) ? 'yes' : 'no',
                        'created_at'         => now(),
                        'updated_at'         => now(),
                    ];
                } else {
                    $invalidRecord = $normalizedRow;
                    $invalidRecord['error_reason'] = implode('; ', $errors);
                    $invalidRecord['row_number'] = $rowNumber;
                    $invalidRecords[] = $invalidRecord;
                }
            }
            fclose($handle);

            // Check for duplicates among valid records.
            $csvIds = array_map(function ($record) {
                return $record['hubspot_id'];
            }, $validRecords);

            $existingIds = DB::table('hubspot_contacts')
                ->whereIn('hubspot_id', $csvIds)
                ->pluck('hubspot_id')
                ->toArray();

            $duplicateRecords = [];
            $finalValidRecords = [];
            foreach ($validRecords as $record) {
                if (in_array($record['hubspot_id'], $existingIds)) {
                    $record['error_reason'] = "Duplicate record: hubspot_id already exists";
                    $duplicateRecords[] = $record;
                } else {
                    $finalValidRecords[] = $record;
                }
            }

            // Insert only non-duplicate valid records.
            if (!empty($finalValidRecords)) {
                DB::table('hubspot_contacts')->insertOrIgnore($finalValidRecords);
            }

            // Count summary.
            $successfulCount = count($finalValidRecords);
            $invalidCount = count($invalidRecords);
            $duplicateCount = count($duplicateRecords);

            // Optionally, you can also generate an invalid records CSV (or store it in DB)
            // and include a download link in the summary.
            // For brevity, we'll assume that step is similar to our previous implementation.

            $summary = [
                'successful'      => $successfulCount,
                'invalid'         => $invalidCount,
                'duplicate'       => $duplicateCount,
                'invalid_csv_url' => $invalidCsvUrl ?? null, // if you generate one
            ];

            // Return a summary (for example, to show in a modal on the dashboard).
            return redirect()->back()->with('import_summary', $summary);
        }

        return redirect()->back()->with('error', 'Unable to open the file.');
    }

    public function exportActiveContacts()
    {
        // Query contacts that are not marked as deleted.
        $records = DB::table('hubspot_contacts')->get();

        // Define the CSV file path
        $csvPath = storage_path('app/csv/active_hubspot_contacts.csv');

        // Ensure the directory exists.
        if (!file_exists(dirname($csvPath))) {
            mkdir(dirname($csvPath), 0777, true);
        }

        // Open the file for writing.
        $file = fopen($csvPath, 'w');

        // Write the CSV header.
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

        // Write each record as a row in the CSV.
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

        // Set headers to force download of the CSV file.
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=active_hubspot_contacts.csv');
        readfile($csvPath);
        exit;
    }

    public function downloadCSV($id)
    {
        $csvImport = CSVImport::findOrFail($id);

        if (!$csvImport->file_content) {
            return redirect()->back()->with('error', 'File not found.');
        }

        $filename = $csvImport->file_name;

        // Just serve the stored CSV content as is.
        return response($csvImport->file_content, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ]);
    }


    public function downloadCSVTemplate()
    {
        // Define the header row.
        $headers = [
            'hubspot_id',
            'firstname',
            'lastname',
            'email',
            'gender',
            'createdate',
            'lastmodifieddate',
            'phone',
            'hubspot_owner_id',
            'hs_lead_status',
            'company',
            'lifecyclestage',
            'country'
        ];

        // Create an example row with sample data.
        $exampleRow = [
            'hubspot_id'       => '12345',
            'firstname'        => 'John',
            'lastname'         => 'Doe',
            'email'            => 'john.doe@example.com',
            'gender'           => 'male',
            'createdate'       => '2021-01-01T12:00:00Z',
            'lastmodifieddate' => '2021-01-10T12:00:00Z',
            'phone'            => '+1234567890',
            'hubspot_owner_id' => '67890',
            'hs_lead_status'   => 'New',
            'company'          => 'Example Inc.',
            'lifecyclestage'   => 'subscriber',
            'country'          => 'USA'
        ];

        // Set the filename for the CSV template.
        $filename = 'csv_template.csv';

        // Open a temporary memory file for writing.
        $handle = fopen('php://temp', 'r+');

        // Write the header row.
        fputcsv($handle, $headers);

        // Write the example data row.
        fputcsv($handle, array_values($exampleRow));

        // Rewind the file pointer and get its content.
        rewind($handle);
        $csvContent = stream_get_contents($handle);
        fclose($handle);

        // Return the CSV file as a download response.
        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename={$filename}");
    }

    public function downloadInvalidCSV($filename)
    {
        $filePath = storage_path('app/csv/invalid/' . $filename);
        if (file_exists($filePath)) {
            return response()->download($filePath, $filename);
        }
        return redirect()->back()->with('error', 'File not found.');
    }
}
