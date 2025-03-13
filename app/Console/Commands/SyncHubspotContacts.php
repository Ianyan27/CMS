<?php

namespace App\Console\Commands;

use App\Http\Controllers\HubspotContactSyncController;
use App\Http\Controllers\HubspotController;
use App\Services\HubspotService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class SyncHubspotContacts extends Command
{
    protected $signature = 'hubspot:sync-contacts {--force : Run sync regardless of schedule} {--modified : Use modified date instead of create date}';
    protected $description = 'Synchronize HubSpot contacts';

    protected $hubspotController;
    protected $hubspotService;

    public function __construct(HubspotContactSyncController $hubspotController, HubspotService $hubspotService)
    {
        parent::__construct();
        $this->hubspotController = $hubspotController;
        $this->hubspotService = $hubspotService;
    }

    public function handle()
    {
        $syncStatus = $this->hubspotService->getSyncStatus('contacts');
        $force = $this->option('force');
        $useModified = $this->option('modified');

        // Check if a sync is already running
        if ($syncStatus->status === 'running') {
            $this->warn('A sync is already in progress. Exiting.');
            return 1;
        }

        // Check if a sync is scheduled and due
        $shouldRun = $force || !$syncStatus->next_sync_timestamp || Carbon::now()->gte($syncStatus->next_sync_timestamp);

        if (!$shouldRun) {
            $this->info('No sync is due at this time. Next sync scheduled for: ' . $syncStatus->next_sync_timestamp->format('Y-m-d H:i:s'));
            return 0;
        }

        if ($useModified) {
            $this->info('Starting HubSpot contact sync using MODIFIED date...');

            // Use the last modified sync timestamp as start date, or default to yesterday
            $startDate = $syncStatus->last_modified_sync_timestamp
                ? $syncStatus->last_modified_sync_timestamp->format('Y-m-d\TH:i:s\Z')
                : Carbon::yesterday()->startOfDay()->format('Y-m-d\TH:i:s\Z');
        } else {
            $this->info('Starting HubSpot contact sync using CREATE date...');

            // Use the last sync timestamp as start date, or default to yesterday
            $startDate = $syncStatus->last_sync_timestamp
                ? $syncStatus->last_sync_timestamp->format('Y-m-d\TH:i:s\Z')
                : Carbon::yesterday()->startOfDay()->format('Y-m-d\TH:i:s\Z');
        }

        // Use current time as end date
        $endDate = Carbon::now()->format('Y-m-d\TH:i:s\Z');

        $this->info("Syncing contacts from {$startDate} to {$endDate}");

        try {
            // Process a single batch
            if ($useModified) {
                $this->hubspotController->processModifiedBatch($startDate, $endDate);
            } else {
                $this->hubspotController->processSingleBatch($startDate, $endDate);
            }

            // Schedule next sync for tomorrow midnight
            $this->hubspotService->updateSyncStatus('contacts', [
                'next_sync_timestamp' => Carbon::tomorrow()->startOfDay(),
            ]);

            $syncType = $useModified ? 'modified' : 'create';
            $this->info("Contact sync by {$syncType} date completed successfully. Next sync scheduled for tomorrow midnight.");

            return 0; // Success
        } catch (\Exception $e) {
            $this->error('Error during contact sync: ' . $e->getMessage());
            Log::error('Automatic sync error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return 1; // Error
        }
    }
}
