<?php

namespace App\Http\Controllers;

use App\Exports\ContactProfileExport;
use App\Models\ContactProfile;
use App\Models\ContactTable;
use App\Models\CSVImport;
use App\Models\HubspotRetrievalHistory;
use App\Models\HubspotContact;
use App\Models\HubspotContactV2;
use App\Services\HubspotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Response;

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
        $totalContacts = ContactTable::count();
        $totalContact = HubspotContactV2::count();
        $totalHubContacts = ContactProfile::count();
        $totalHubspotContactProfile = ContactTable::count();
        $csvImports = CSVImport::orderBy('created_at', 'desc')->get();
        $lastSyncDate = $syncStatus->last_successful_sync;

        // Get next start date (which is the last end date)
        $nextStartDate = $syncStatus->last_sync_timestamp ?? '2021-10-07T00:00:00Z';
        $endDate = Carbon::now()->format('Y-m-d\TH:i:s\Z');

        return view('hubspot.dashboard', compact(
            'syncStatus',
            'totalContacts',
            'totalContact',
            'totalHubContacts',
            'lastSyncDate',
            'nextStartDate',
            'endDate',
            'csvImports',
            'totalHubspotContactProfile',
        ));
    }

    public function viewContactsV2()
    {
        $contacts = HubspotContactV2::paginate(20);
        return view('hubspot.contacts-v2', compact('contacts'));
    }

    public function displayHubspotContacts()
    {
        return view('hubspot.display-hubspot-contacts');
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
        $chunkSize = 100000; // Process in chunks of 3000 for efficiency

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

        $existingCount = DB::table(table: 'hubspot_contacts_v2')
            ->whereIn('hubspot_id', $hubspotIds)
            ->count();

        if ($existingCount > 0) {
            Log::info("Found {$existingCount} existing contacts that will be updated with HubSpot data");
        }

        // Process in chunks for efficiency
        $this->processContactsBatch($contacts, $chunkSize);
    }

    // private function processContactsBatch($contacts, $chunkSize)
    // {
    //     $chunks = array_chunk($contacts, $chunkSize);

    //     Log::info("Processing contact batch", [
    //         'contactCount' => count($contacts),
    //         'chunks'       => count($chunks),
    //         'chunkSize'    => $chunkSize
    //     ]);

    //     foreach ($chunks as $chunkIndex => $chunk) {
    //         foreach ($chunk as $contactIndex => $contact) {
    //             try {
    //                 if (!isset($contact['id']) || !isset($contact['properties'])) {
    //                     Log::warning("Invalid contact format detected", [
    //                         'chunk' => $chunkIndex,
    //                         'index' => $contactIndex,
    //                         'raw'   => $contact
    //                     ]);
    //                     continue;
    //                 }

    //                 $props = $contact['properties'];
    //                 Log::info("Logging contact properties", ['hubspot_id' => $contact['id'], 'properties' => $props]);

    //                 $hubspotId = $contact['id'];

    //                 // Build core record from HubSpot contact
    //                 $record = [
    //                     'hubspot_id'             => $hubspotId,
    //                     'contact_source'         => $props['ad_channel'] ?? null,
    //                     'contact_email'          => $props['email'] ?? null,
    //                     'contact_lastname'       => $props['lastname'] ?? null,
    //                     'contact_firstname'      => $props['firstname'] ?? null,
    //                     'contact_mobile'         => $props['phone'] ?? null,
    //                     'linkedin_id'            => $props['hs_linkedin_url'] ?? null,
    //                     'passport_full_name'     => $props['full_name_of_student__as_in_nric_'] ?? null,
    //                     'nric_id'                => $props['nric_number__for_sc_pr_'] ?? null,
    //                     'passport_id'            => $props['passport_number___fin__indicate_n_a_if_not_applicable___sgret_'] ?? null,
    //                     'date_of_birth'          => $props['age__sgret_'] ?? null,
    //                     'race'                   => $props['race'] ?? null,
    //                     'nationality'            => $props['nationality'] ?? null,
    //                     'parent_name'            => $props['parent_guardian_contact_no___for_student_under_18_years_old__enter_n_a_if_not_applicable_'] ?? null,
    //                     'highest_qualification'  => $props['highest_level_of_education'] ?? null,
    //                     'business_unit'          => $props['business_unit'] ?? null,
    //                     'work_experience_yrs'    => $props['how_many_years_of_work_experience_do_you_have'] ?? null,
    //                     'current_company'        => $props['current_or_last_company'] ?? null,
    //                     'company_classification' => $props['company_type'] ?? null,
    //                     'current_job_role'       => $props['jobtitle'] ?? null,
    //                 ];

    //                 // Insert ContactProfile
    //                 $contactProfile = ContactProfile::updateOrCreate(
    //                     ['hubspot_id' => $hubspotId],
    //                     $record
    //                 );

    //                 if (!$contactProfile->contact_id) {
    //                     Log::error("ContactProfile insertion failed", [
    //                         'hubspot_id' => $hubspotId
    //                     ]);
    //                     continue;
    //                 }

    //                 Log::info("Inserted ContactProfile", [
    //                     'hubspot_id' => $hubspotId,
    //                     'contact_id' => $contactProfile->contact_id
    //                 ]);

    //                 // Insert/Update ContactEngagementStatus
    //                 $engagementStatus = ContactEngagementStatus::updateOrCreate(
    //                     ['contact_id' => $contactProfile->contact_id],
    //                     [
    //                         'contact_mgr'       => $props["account_manager__hed_"] ?? null,
    //                         'contact_exec'      => $props["hubspot_owner_id"] ?? null,
    //                         'contact_status'    => $props["contact_status"] ?? null,
    //                         'cilos_stage'       => $props["lifecyclestage"] ?? null,
    //                         'cilos_substage'    => $props["sales_lifecycle_l2"] ?? null,
    //                         'product_interest'  => $props["which_course_are_you_interested_in_"] ?? null,
    //                         'lead_status'       => $props["hs_lead_status"] ?? null,
    //                     ]
    //                 );

    //                 Log::info("Inserted ContactEngagementStatus", [
    //                     'contact_id' => $contactProfile->contact_id,
    //                     'engagement_id' => $engagementStatus->contact_engagement_status_id
    //                 ]);

    //                 // Insert/Update ContactActivitiesStatus
    //                 $activityStatus = ContactActivitiesStatus::updateOrCreate(
    //                     ['contact_id' => $contactProfile->contact_id],
    //                     [
    //                         'last_messaging_date' => $props["notes_last_updated"] ?? null,
    //                     ]
    //                 );

    //                 Log::info("Inserted ContactActivitiesStatus", [
    //                     'contact_id' => $contactProfile->contact_id,
    //                     'activity_id' => $activityStatus->contact_activities_status_id
    //                 ]);

    //                 // Final enrichment of ContactProfile
    //                 $engagementData = [
    //                     'account_manager_hed_'                => $engagementStatus->contact_mgr,
    //                     'hubspot_owner_id'                    => $engagementStatus->contact_exec,
    //                     'contact_status'                      => $engagementStatus->contact_status,
    //                     'lifecyclestage'                      => $engagementStatus->cilos_stage,
    //                     'sales_lifecycle_l2'                  => $engagementStatus->cilos_substage,
    //                     'which_course_are_you_interested_in_' => $engagementStatus->product_interest,
    //                     'hs_lead_status'                      => $engagementStatus->hs_lead_status,
    //                 ];

    //                 $activityData = [
    //                     'last_messaging_date' => $activityStatus->last_messaging_date,
    //                 ];

    //                 ContactProfile::updateOrCreate(
    //                     ['hubspot_id' => $hubspotId],
    //                     array_merge($record, $engagementData, $activityData)
    //                 );
    //             } catch (\Exception $e) {
    //                 Log::error("Error processing contact", [
    //                     'hubspot_id' => $contact['id'] ?? null,
    //                     'error'      => $e->getMessage(),
    //                     'trace'      => $e->getTraceAsString()
    //                 ]);
    //                 continue;
    //             }
    //         }
    //     }
    // }

    /**
     * Process a batch of contacts by chunks
     */
    private function processContactsBatch($contacts, $chunkSize = 1000)
    {
        $chunks = array_chunk($contacts, $chunkSize);
        Log::info("Processing contact batch", [
            'contactCount' => count($contacts),
            'chunks'       => count($chunks),
            'chunkSize'    => $chunkSize
        ]);

        foreach ($chunks as $chunkIndex => $chunk) {
            $records = [];

            foreach ($chunk as $contact) {
                try {
                    if (!isset($contact['id'], $contact['properties']) || !is_array($contact['properties'])) {
                        Log::warning('Skipping malformed contact', ['contact' => $contact]);
                        continue;
                    }

                    $properties = $contact['properties'];

                    $records[] = [
                        'hubspot_id'             => $contact['id'],
                        'contact_source'         => $properties['ad_channel'] ?? null,
                        'contact_email'          => $properties['email'] ?? null,
                        'contact_lastname'       => $properties['lastname'] ?? null,
                        'contact_firstname'      => $properties['firstname'] ?? null,
                        'contact_mobile'         => $properties['phone'] ?? null,
                        'linkedin_id'            => $properties['linkedin_profile'] ?? null,
                        //'updated_linkedin_id'    => $properties['hs_linkedin_url'] ?? null,
                        'passport_full_name'     => $properties['full_name_of_student__as_in_nric_'] ?? null,
                        'nric_id'                => $properties['nric_number__for_sc_pr_'] ?? null,
                        'passport_id'            => $properties['passport_number___fin__indicate_n_a_if_not_applicable___sgret_'] ?? null,
                        'date_of_birth'          => $properties['age__sgret_'] ?? null,
                        'race'                   => $properties['race'] ?? null,
                        'nationality'            => $properties['nationality'] ?? null,
                        'parent_name'            => $properties['parent_guardian_contact_no___for_student_under_18_years_old__enter_n_a_if_not_applicable_'] ?? null,
                        'parent_email_id'        => $properties['parent_guardian_email'] ?? null,
                        'highest_qualification'  => $properties['highest_level_of_education'] ?? null,
                        'business_unit'          => $properties['business_unit'] ?? null,
                        'work_experience_yrs'    => $properties['how_many_years_of_work_experience_do_you_have'] ?? null,
                        'current_company'        => $properties['current_or_last_company'] ?? null,
                        'company_classification' => $properties['company_type'] ?? null,
                        'current_job_role'       => $properties['jobtitle'] ?? null,
                        'contact_mgr'            => $properties['account_manager__hed_'] ?? null,
                        'contact_exec'           => $properties['hubspot_owner_id'] ?? null,
                        'contact_status'         => $properties['contact_status'] ?? null,
                        'cilos_stage'            => $properties['lifecyclestage'] ?? null,
                        'cilos_substage'         => $properties['sales_lifecycle_l2'] ?? null,
                        'lead_status'            => $properties['hs_lead_status'] ?? null,
                        'product_interest'       => $properties['which_course_are_you_interested_in_'] ?? null,
                        'last_messaging_date'    => $properties['notes_last_updated'] ?? null,
                        'hubspot_created_at'     => $properties['createdate'] ?? null,
                        'hubspot_updated_at'     => $properties['lastmodifieddate'] ?? null,
                        'created_at'             => now(),
                        'updated_at'             => now(),
                    ];
                } catch (\Throwable $e) {
                    Log::error('Error processing contact', [
                        'message' => $e->getMessage(),
                        'contact' => $contact
                    ]);
                }
            }

            // Sub-batch insert to stay under MySQL placeholder limit
            $safeBatchSize = 500; // 500 rows * 28 cols = 14,000 placeholders
            $safeChunks = array_chunk($records, $safeBatchSize);

            foreach ($safeChunks as $subIndex => $safeChunk) {
                try {
                    DB::table('hubpsot_contact_profile_2nd_copy')->upsert(
                        $safeChunk,
                        ['hubspot_id'],
                        [
                            'contact_source',
                            'contact_email',
                            'contact_lastname',
                            'contact_firstname',
                            'contact_mobile',
                            'linkedin_id',
                            //'updated_linkedin_id',
                            'passport_full_name',
                            'nric_id',
                            'passport_id',
                            'date_of_birth',
                            'race',
                            'nationality',
                            'parent_name',
                            'parent_email_id',
                            'highest_qualification',
                            'business_unit',
                            'work_experience_yrs',
                            'current_company',
                            'company_classification',
                            'current_job_role',
                            'contact_mgr',
                            'contact_exec',
                            'contact_status',
                            'cilos_stage',
                            'cilos_substage',
                            'lead_status',
                            'product_interest',
                            'last_messaging_date',
                            'updated_at',
                            'hubspot_created_at',
                            'hubspot_updated_at',
                        ]
                    );
                } catch (\Throwable $e) {
                    Log::error("Failed inserting sub-batch {$chunkIndex}.{$subIndex}", [
                        'message' => $e->getMessage(),
                        'records' => $safeChunk
                    ]);
                }
            }
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
        // Log::info("Generating XLSX and saving to storage");
        // ini_set('memory_limit', '1024M'); // just in case

        // $filePath = 'exports/contact-profile.xlsx';
        // $saved = Excel::store(new ContactProfileExport, $filePath);

        // if ($saved) {
        //     Log::info("✅ Excel file saved to: storage/app/$filePath");
        //     return redirect()->back()->with('success', 'Excel file saved!')->with('path', storage_path("app/$filePath"));
        // } else {
        //     Log::error("❌ Excel file NOT saved.");
        //     return response()->json([
        //         'error' => 'Excel file could not be saved.'
        //     ], 500);
        // }

        // Query contacts that are not marked as deleted.
        $records = DB::table('Hubspot_Contact_Profile')
            ->where('temp_id', '<', 20001)
            ->get();

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="active_hubspot_contacts.csv"',
        ];

        $callback = function () use ($records) {
            $out = fopen('php://output', 'w');

            fputcsv($out, [
                'contact_id',
                'hubspot_id',
                'contact_source',
                'contact_email',
                'contact_lastname',
                'contact_firstname',
                'contact_mobile',
                'linkedin_id',
                'facebook_id',
                'passport_full_name',
                'nric_id',
                'passport_id',
                'date_of_birth',
                'race',
                'nationality',
                'parent_name',
                'parent_email_id',
                'parent_passport_id',
                'highest_qualification',
                'qualifications_list',
                'business_unit',
                'academic_aptitude',
                'career_segment',
                'work_experience_yrs',
                'current_company',
                'company_classification',
                'current_job_role',
                'job_classification',
                'career_level',
                'contact_cv',
                'general_ksa_profile',
                'digital_skills_profile',
                'management_skills_profile',
                'stem_skills',
                'coding_skills',
                'ai_skills',
                'digital_marketing_skills',
                'applications_skills',
                'project_magt_skills',
                'business_leader_skills',
                'customer_magt_skills',
                'contact_persona',
                'sales_affiliate',
                'contact_mgr',
                'contact_exec',
                'managed_contact_yn',
                'contact_status',
                'cilos_status',
                'cilos_stage',
                'cilos_substage',
                'win_lost_reasons',
                'proposed_solution',
                'product_interest',
                'last_messaging_date',
                'last_messaging_contents',
                'last_campaign_date',
                'last_campaign_contents',
                'last_digital_conversation_date',
                'digital_conversation_contents',
                'campaign_engagement_contents',
                'messaging_engagement_score',
                'messaging_sentiment_score',
                'conversation_engagement_score',
                'leads_score',
                'leads_score_summary'
            ]);

            foreach ($records as $row) {
                fputcsv($out, [
                    $row->contact_id,
                    $row->hubspot_id,
                    $row->contact_source,
                    $row->contact_email,
                    $row->contact_lastname,
                    $row->contact_firstname,
                    $row->contact_mobile,
                    $row->linkedin_id,
                    $row->facebook_id,
                    $row->passport_full_name,
                    $row->nric_id,
                    $row->passport_id,
                    $row->date_of_birth,
                    $row->race,
                    $row->nationality,
                    $row->parent_name,
                    $row->parent_email_id,
                    $row->parent_passport_id,
                    $row->highest_qualification,
                    $row->qualifications_list,
                    $row->business_unit,
                    $row->academic_aptitude,
                    $row->career_segment,
                    $row->work_experience_yrs,
                    $row->current_company,
                    $row->company_classification,
                    $row->current_job_role,
                    $row->job_classification,
                    $row->career_level,
                    $row->contact_cv,
                    $row->general_ksa_profile,
                    $row->digital_skills_profile,
                    $row->management_skills_profile,
                    $row->stem_skills,
                    $row->coding_skills,
                    $row->ai_skills,
                    $row->digital_marketing_skills,
                    $row->applications_skills,
                    $row->project_magt_skills,
                    $row->business_leader_skills,
                    $row->customer_magt_skills,
                    $row->contact_persona,
                    $row->sales_affiliate,
                    $row->contact_mgr,
                    $row->contact_exec,
                    $row->managed_contact_yn,
                    $row->contact_status,
                    $row->cilos_status,
                    $row->cilos_stage,
                    $row->cilos_substage,
                    $row->win_lost_reasons,
                    $row->proposed_solution,
                    $row->product_interest,
                    $row->last_messaging_date,
                    $row->last_messaging_contents,
                    $row->last_campaign_date,
                    $row->last_campaign_contents,
                    $row->last_digital_conversation_date,
                    $row->digital_conversation_contents,
                    $row->campaign_engagement_contents,
                    $row->messaging_engagement_score,
                    $row->messaging_sentiment_score,
                    $row->conversation_engagement_score,
                    $row->leads_score,
                    $row->leads_score_summary
                ]);
            }

            fclose($out);
        };

        return Response::stream($callback, 200, $headers);
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
            //Contact Profile`
            'contact_id',
            'hubspot_id',
            'contact_source',
            'contact_email',
            'contact_lastname',
            'contact_firstname',
            'contact_mobile',
            'linkedin_id',
            'facebook_id',
            'passport_full_name',
            'nric_id',
            'passport_id',
            'date_of_birth',
            'race',
            'nationality',
            'parent_name',
            'parent_email_id',
            'parent_passport_id',
            'highest_qualification',
            'qualifications_list',
            'business_unit',
            'academic_aptitude',
            'career_segment',
            'work_experience_yrs',
            'current_company',
            'company_classification',
            'current_job_role',
            'job_classification',
            'career_level',
            'contact_cv',
            'general_ksa_profile',
            'digital_skills_profile',
            'management_skills_profile',
            'stem_skills',
            'coding_skills',
            'ai_skills',
            'digital_marketing_skills',
            'applications_skills',
            'project_magt_skills',
            'business_leader_skills',
            'customer_magt_skills',
            'contact_persona',
            'sales_affiliate',

            //Contact Engagement Status
            'contact_mgr',
            'contact_exec',
            'managed_contact__yn',
            'contact_status',
            'cilos_status',
            'cilos_stage',
            'cilos_substage',
            'win_lost_reasons',
            'proposed_solution',
            'product_interest',

            //Contact Activities Status
            'last_messaging_date',
            'last_messaging_contents',
            'last_campaign_date',
            'last_campaign_contents',
            'last_digital_conversation_date',
            'digital_conversation_contents',
            'campaign_engagement_contents',
            'messaging_engagement_score',
            'messaging_sentiment_score',
            'conversation_engagement_score',
            'leads_score',
            'leads_score_summary'
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

    public function checkStatus()
    {
        $syncStatus = $this->hubspotService->getSyncStatus('contacts');

        return response()->json([
            'status' => $syncStatus->status,
            'last_sync' => $syncStatus->last_sync_timestamp,
            'total_synced' => $syncStatus->total_synced
        ]);
    }
}
