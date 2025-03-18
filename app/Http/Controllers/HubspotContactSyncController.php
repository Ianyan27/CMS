<?php

namespace App\Http\Controllers;

use App\Models\HubspotRetrievalHistory;
use App\Models\HubspotContact;
use App\Models\HubspotSyncStatus;
use App\Models\HubspotContactBuffer;
use App\Services\HubspotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

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
        $lastSyncDate = $syncStatus->last_successful_sync;

        // Get next start date (which is the last end date)
        $nextStartDate = $syncStatus->last_sync_timestamp ?? '2021-10-07T00:00:00Z';
        $endDate = Carbon::now()->format('Y-m-d\TH:i:s\Z');

        return view('hubspot.dashboard', compact(
            'syncStatus',
            'totalContacts',
            'lastSyncDate',
            'nextStartDate',
            'endDate'
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
            'chunks' => count($chunks),
            'chunkSize' => $chunkSize
        ]);

        foreach ($chunks as $chunk) {
            $records = [];

            foreach ($chunk as $contact) {
                $records[] = [
                    'hubspot_id' => $contact['id'],
                    'email' => $contact['properties']['email'] ?? null,
                    'firstname' => $contact['properties']['firstname'] ?? null,
                    'lastname' => $contact['properties']['lastname'] ?? null,
                    'gender' => $contact['properties']['gender'] ?? null,

                    // Convert HubSpot date strings to Carbon instances
                    'hubspot_created_at' => isset($contact['properties']['createdate'])
                        ? Carbon::parse($contact['properties']['createdate'])
                        : null,
                    'hubspot_updated_at' => isset($contact['properties']['lastmodifieddate'])
                        ? Carbon::parse($contact['properties']['lastmodifieddate'])
                        : null,

                    // Newly added columns
                    'phone'             => $contact['properties']['phone'] ?? null,
                    'hubspot_owner_id'  => $contact['properties']['hubspot_owner_id'] ?? null,
                    'hs_lead_status'    => $contact['properties']['hs_lead_status'] ?? null,
                    'company'           => $contact['properties']['company'] ?? null,
                    'lifecyclestage'    => $contact['properties']['lifecyclestage'] ?? null,
                    'country'           => $contact['properties']['country'] ?? null,

                    'created_at'        => Carbon::now(),
                    'updated_at'        => Carbon::now(),
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

                    // Newly added columns
                    'phone',
                    'hubspot_owner_id',
                    'hs_lead_status',
                    'company',
                    'lifecyclestage',
                    'country',
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
        // Validate that the file exists, is a CSV file, and does not exceed the max size (2MB).
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048'
        ]);

        $file = $request->file('file');

        if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
            // Read the header row.
            $header = fgetcsv($handle, 1000, ',');

            /* 
         * Define expected headers with alternative names.
         * Required fields: hubspot_id, firstname, lastname, email.
         * The remaining fields are optional.
         */
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

            // Check required fields.
            $requiredFields = ['hubspot_id', 'firstname', 'lastname', 'email'];
            $missingFields = [];
            foreach ($requiredFields as $field) {
                $found = false;
                foreach ($expectedHeaders[$field] as $alt) {
                    if (in_array($alt, $header)) {
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $missingFields[] = $field;
                }
            }
            if (!empty($missingFields)) {
                fclose($handle);
                $missingList = implode(', ', $missingFields);
                return redirect()->back()->with('error', "Invalid CSV file. Missing required columns: {$missingList}");
            }

            $records = [];

            // Process each row.
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                // Normalize the row based on the expected headers.
                $normalizedRow = [];
                foreach ($expectedHeaders as $standard => $alternatives) {
                    $normalizedRow[$standard] = null;
                    foreach ($alternatives as $alt) {
                        // Find the position of the alternative header in the CSV header row.
                        $pos = array_search($alt, $header);
                        if ($pos !== false && isset($row[$pos])) {
                            $normalizedRow[$standard] = $row[$pos];
                            break;
                        }
                    }
                }

                $records[] = [
                    'hubspot_id'         => $normalizedRow['hubspot_id'] ?? null,
                    'email'              => $normalizedRow['email'] ?? null,
                    'firstname'          => $normalizedRow['firstname'] ?? null,
                    'lastname'           => $normalizedRow['lastname'] ?? null,
                    'gender'             => $normalizedRow['gender'] ?? null,
                    'hubspot_created_at' => isset($normalizedRow['createdate'])
                        ? Carbon::parse($normalizedRow['createdate'])
                        : null,
                    'hubspot_updated_at' => isset($normalizedRow['lastmodifieddate'])
                        ? Carbon::parse($normalizedRow['lastmodifieddate'])
                        : null,
                    'phone'              => $normalizedRow['phone'] ?? null,
                    'hubspot_owner_id'   => $normalizedRow['hubspot_owner_id'] ?? null,
                    'hs_lead_status'     => $normalizedRow['hs_lead_status'] ?? null,
                    'company'            => $normalizedRow['company'] ?? null,
                    'lifecyclestage'     => $normalizedRow['lifecyclestage'] ?? null,
                    'country'            => $normalizedRow['country'] ?? null,
                    // Randomly assign marked_deleted ("yes" or "no").
                    'marked_deleted'     => (rand(0, 1) === 1) ? 'yes' : 'no',
                    'created_at'         => Carbon::now(),
                    'updated_at'         => Carbon::now(),
                ];
            }
            fclose($handle);

            // Extract all hubspot_ids from the CSV records.
            $csvIds = array_map(function ($record) {
                return $record['hubspot_id'];
            }, $records);

            // Query the database for existing contacts with these hubspot_ids.
            $existingIds = DB::table('hubspot_contacts')
                ->whereIn('hubspot_id', $csvIds)
                ->pluck('hubspot_id')
                ->toArray();

            // Count duplicates.
            $duplicateCount = count($existingIds);

            if ($duplicateCount > 0) {
                Log::info('The following HubSpot contacts already exist and were skipped: ' . implode(', ', $existingIds));
            }

            // Insert records and ignore duplicates.
            DB::table('hubspot_contacts')->insertOrIgnore($records);

            // Prepare flash message.
            $message = "CSV imported successfully!";
            if ($duplicateCount > 0) {
                $message .= " {$duplicateCount} duplicate contact(s) were skipped.";
            }

            return redirect()->back()->with('success', $message);
        }

        return redirect()->back()->with('error', 'Unable to open the file.');
    }

    public function exportActiveContacts()
    {
        // Query contacts that are not marked as deleted.
        $records = DB::table('hubspot_contacts')
            ->where('marked_deleted', 'no')
            ->get();

        // Define the CSV file path (you can adjust as needed).
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
            'marked_deleted',
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
                $record->marked_deleted,
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
}
