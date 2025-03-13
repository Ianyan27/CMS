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

    /**
     * Display dashboard view with manual sync button
     */
    public function dashboard()
    {
        $syncStatus = $this->hubspotService->getSyncStatus('contacts');
        $totalContacts = HubspotContact::count();
        $lastSyncDate = $syncStatus->last_successful_sync;

        // Get next start date (which is the last end date)
        $nextStartDate = $syncStatus->last_sync_timestamp ?? '2020-03-01T00:00:00Z';
        $endDate = Carbon::now()->format('Y-m-d\TH:i:s\Z');

        return view('hubspot.dashboard', compact(
            'syncStatus',
            'totalContacts',
            'lastSyncDate',
            'nextStartDate',
            'endDate'
        ));
    }

    /**
     * Display sync history view
     */
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

    /**
     * Trigger manual sync process with a single batch
     */
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

    /**
     * Process a single batch of contacts
     */
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

            // Process contacts in chunks of 3000
            $this->processContacts($contacts);

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
            Log::error("HubSpot sync error", [
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

    /**
     * Process contacts and insert directly to database
     */
    private function processContacts($contacts)
    {
        // Process in chunks of 3000 for efficiency (updated from 1000)
        $chunks = array_chunk($contacts, 3000);

        foreach ($chunks as $chunk) {
            $records = [];

            foreach ($chunk as $contact) {
                $records[] = [
                    'hubspot_id' => $contact['id'],
                    'email' => $contact['properties']['email'] ?? null,
                    'firstname' => $contact['properties']['firstname'] ?? null,
                    'lastname' => $contact['properties']['lastname'] ?? null,
                    'gender' => $contact['properties']['gender'] ?? null,
                    'hubspot_created_at' => isset($contact['properties']['createdate'])
                        ? Carbon::parse($contact['properties']['createdate'])
                        : null,
                    'hubspot_updated_at' => isset($contact['properties']['lastmodifieddate'])
                        ? Carbon::parse($contact['properties']['lastmodifieddate'])
                        : null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }

            // Use upsert to handle duplicates
            DB::table('hubspot_contacts')->upsert(
                $records,
                ['hubspot_id'],
                ['email', 'firstname', 'lastname', 'gender', 'hubspot_updated_at', 'updated_at']
            );

            Log::info("Inserted batch of " . count($records) . " contacts");
        }
    }


    /**
     * Cancel an ongoing sync
     */
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

    /**
     * Trigger manual sync process by modified date
     */
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
}
