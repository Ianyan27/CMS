<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\HubspotSyncStatus;

class HubspotService
{
    protected $apiKey;
    protected $baseUrl = 'https://api.hubapi.com';

    public function __construct()
    {
        $this->apiKey = config('services.hubspot.api_key');
    }

    /**
     * Search contacts with specified filters
     */
    public function searchContacts($startDate, $endDate, $limit = 100, $after = null)
    {
        // Always use Bearer token authentication since that's what works in your environment
        $url = "{$this->baseUrl}/crm/v3/objects/contacts/search";

        $payload = [
            'filterGroups' => [
                [
                    'filters' => [
                        [
                            'propertyName' => 'createdate',
                            'operator' => 'GT',
                            'value' => $startDate
                        ],
                        [
                            'propertyName' => 'createdate',
                            'operator' => 'LT',
                            'value' => $endDate
                        ]
                    ]
                ]
            ],
            // 'properties' => [
            //     'firstname',
            //     'lastname',
            //     'gender',
            //     'email',
            //     'phone',
            //     'hubspot_owner_id',
            //     'hs_lead_status',
            //     'company',
            //     'lifecyclestage',
            //     'country',
            //     'createdate',
            //     'lastmodifieddate
            // '
            'properties' => [
                "ad_channel",
                "email",
                "lastname",
                "firstname",
                "phone",
                "linkedin_profile",
                "full_name_of_student__as_in_nric_",
                "nric_number__for_sc_pr_",
                "passport_number___fin__indicate_n_a_if_not_applicable___sgret_",
                "age__sgret_",
                "race",
                "nationality",
                "parent_guardian_contact_no___for_student_under_18_years_old__enter_n_a_if_not_applicable_",
                "highest_level_of_education",
                "business_unit",
                "how_many_years_of_work_experience_do_you_have",
                "current_or_last_company",
                "company_type",
                "jobtitle",
                "account_manager__hed_",
                "hubspot_owner_id",
                "contact_status",
                "lifecyclestage",
                "sales_lifecycle_l2",
                "which_course_are_you_interested_in_",
                "hs_lead_status",
                "notes_last_updated"
            ],
            'limit' => $limit,
        ];

        if ($after) {
            $payload['after'] = $after;
        }

        // Set a reasonable timeout to prevent hanging
        $response = Http::timeout(15)->withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
            'Content-Type' => 'application/json',
        ])->post($url, $payload);

        if ($response->failed()) {
            Log::error('HubSpot API error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            throw new \Exception('HubSpot API error: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Count total contacts between date range
     */
    public function countContacts($startDate, $endDate)
    {
        $result = $this->searchContacts($startDate, $endDate, 1);
        return $result['total'] ?? 0;
    }

    /**
     * Adaptive time window algorithm to find optimal batch size
     * Returns the adjusted end date and total count
     */
    public function findOptimalTimeWindow($startDate, $endDate)
    {
        $minBatchSize = 3000;      // Minimum contacts to retrieve in a batch
        $maxBatchSize = 9999;      // Maximum contacts to retrieve in a batch
        $extremeBatchThreshold = 15000; // New threshold for "extremely large" batches
        $optimalEndDate = $endDate;

        // Set script timeout for CLI usage.
        if (php_sapi_name() === 'cli') {
            set_time_limit(6000); // 15 minutes
        }

        // Get the initial count of contacts within the full window.
        $totalContacts = $this->countContacts($startDate, $optimalEndDate);
        Log::info("Starting adaptive time window adjustment", [
            'startDate'    => $startDate,
            'endDate'      => $optimalEndDate,
            'initialCount' => $totalContacts
        ]);

        // Array of thresholds with their initial reduction (in hours) and labels.
        $reductions = [
            200000 => ['reduction' => 144, 'label' => '>200k'],
            175000 => ['reduction' => 120, 'label' => '>175k'],
            150000 => ['reduction' => 120, 'label' => '>150k'],
            125000 => ['reduction' => 96,  'label' => '>125k'],
            100000 => ['reduction' => 72,  'label' => '>100k'],
            75000 => ['reduction' => 48,  'label' => '>75k'],
            50000 => ['reduction' => 24,  'label' => '>50k'],
            30000 => ['reduction' => 12,  'label' => '>30k'],
            25000 => ['reduction' => 8,   'label' => '>25k'],
            22500 => ['reduction' => 6,   'label' => '>22.5k'],
            17500 => ['reduction' => 3,   'label' => '>17.5k'],
            12500 => ['reduction' => 1,   'label' => '>12.5k'],
        ];

        // Apply each threshold-based reduction sequentially.
        foreach ($reductions as $threshold => $params) {
            if ($totalContacts > $threshold) {
                $this->reduceWindow($startDate, $optimalEndDate, $totalContacts, $threshold, $params['reduction'], 'hours', $params['label']);
            }
        }

        // STEP 0: Extreme reduction for contacts over extremeBatchThreshold (15k)
        if ($totalContacts > $extremeBatchThreshold) {
            $this->reduceWindow($startDate, $optimalEndDate, $totalContacts, $extremeBatchThreshold, 12, 'hours', 'extreme (>15k)');
        }

        // STEP 1: Fine tune the window if the total is still above the max batch size (9,999)
        $iterationCount = 0;
        $maxIterations = 10;
        $currentReduction = 1; // in hours
        while ($totalContacts > $maxBatchSize) {
            if ($iterationCount >= $maxIterations) {
                $currentReduction *= 2;
                $iterationCount = 0;
                Log::warning("Increasing reduction rate in STEP 1: now subtracting {$currentReduction} hours per iteration");
            }
            $optimalEndDate = Carbon::parse($optimalEndDate)
                ->subHours($currentReduction)
                ->format('Y-m-d\TH:i:s\Z');
            $totalContacts = $this->countContacts($startDate, $optimalEndDate);
            Log::info("Reduced window by {$currentReduction} hour(s) in STEP 1", [
                'newEndDate' => $optimalEndDate,
                'newCount'   => $totalContacts
            ]);

            // If the window nears the start date, switch to a 30-minute reduction.
            if (Carbon::parse($optimalEndDate)->lte(Carbon::parse($startDate)->addDay())) {
                Log::warning("End date approaching start date - attempting 30-min reduction");
                $innerIteration = 0;
                $innerMaxIterations = 10;
                $innerReduction = 30; // in minutes
                while ($totalContacts > $maxBatchSize) {
                    if ($innerIteration >= $innerMaxIterations) {
                        $innerReduction *= 2;
                        $innerIteration = 0;
                        Log::warning("Increasing reduction rate in nested loop: now subtracting {$innerReduction} minutes per iteration");
                    }
                    $optimalEndDate = Carbon::parse($optimalEndDate)
                        ->subMinutes($innerReduction)
                        ->format('Y-m-d\TH:i:s\Z');
                    $totalContacts = $this->countContacts($startDate, $optimalEndDate);
                    Log::info("Reduced window by {$innerReduction} minutes within the 1-day buffer", [
                        'newEndDate' => $optimalEndDate,
                        'newCount'   => $totalContacts
                    ]);
                    if (Carbon::parse($optimalEndDate)->lte(Carbon::parse($startDate))) {
                        Log::warning("We've reached the start date in the sub loop. Stopping 30-min reduction.");
                        break;
                    }
                    $innerIteration++;
                }
                break;
            }
            $iterationCount++;
        }

        // STEP 2: Increase window if too few contacts, starting with 1 minute increments.
        $iterationCount = 0;
        $maxIterations = 10;
        $currentIncrement = 1; // in minutes
        while ($totalContacts < $minBatchSize) {
            if ($iterationCount >= $maxIterations) {
                // Increase increment to 5 minutes if we've hit max iterations.
                $currentIncrement = 5;
                $iterationCount = 0;
                Log::warning("Increasing addition rate in STEP 2: now adding {$currentIncrement} minutes per iteration");
            }

            $optimalEndDate = Carbon::parse($optimalEndDate)
                ->addMinutes($currentIncrement)
                ->format('Y-m-d\TH:i:s\Z');

            // Do not exceed the original end date.
            if (Carbon::parse($optimalEndDate)->gt(Carbon::parse($endDate))) {
                $optimalEndDate = $endDate;
                $totalContacts = $this->countContacts($startDate, $optimalEndDate);
                Log::info("Reached original end date during increase phase", [
                    'finalEndDate' => $optimalEndDate,
                    'finalCount'   => $totalContacts
                ]);
                break;
            }

            $totalContacts = $this->countContacts($startDate, $optimalEndDate);
            Log::info("Increased window by {$currentIncrement} minute(s) in STEP 2", [
                'newEndDate' => $optimalEndDate,
                'newCount'   => $totalContacts
            ]);
            $iterationCount++;
        }

        // STEP 3: Final fine-tuning: if still over max batch size, reduce by seconds.
        $iterationCount = 0;
        $maxIterations = 10;
        $currentReduction = 5; // in seconds
        while ($totalContacts > $maxBatchSize) {
            if ($iterationCount >= $maxIterations) {
                $currentReduction *= 2;
                $iterationCount = 0;
                Log::warning("Increasing fine-tuning reduction rate: now subtracting {$currentReduction} seconds per iteration");
            }
            $optimalEndDate = Carbon::parse($optimalEndDate)
                ->subSeconds($currentReduction)
                ->format('Y-m-d\TH:i:s\Z');
            $totalContacts = $this->countContacts($startDate, $optimalEndDate);
            Log::info("Fine-tuning: reduced window by {$currentReduction} seconds", [
                'newEndDate' => $optimalEndDate,
                'newCount'   => $totalContacts
            ]);
            $iterationCount++;
        }

        Log::info("Final time window adjustment result", [
            'startDate'      => $startDate,
            'endDate'        => $optimalEndDate,
            'totalContacts'  => $totalContacts,
            'isWithinLimits' => ($totalContacts >= $minBatchSize && $totalContacts <= $maxBatchSize) ? 'Yes' : 'No'
        ]);

        return [
            'endDate'       => $optimalEndDate,
            'totalContacts' => $totalContacts
        ];
    }

    /**
     * Helper function to reduce the optimalEndDate until the totalContacts is below the specified threshold.
     *
     * @param string $startDate        The start date.
     * @param string &$optimalEndDate  Reference to the current optimal end date.
     * @param int    &$totalContacts   Reference to the current total contacts count.
     * @param int    $threshold        The contacts threshold to get below.
     * @param int    $initialReduction The initial reduction amount.
     * @param string $unit             The unit of reduction (e.g., 'hours', 'minutes', 'seconds').
     * @param string $logLabel         A label for logging purposes.
     */
    private function reduceWindow($startDate, &$optimalEndDate, &$totalContacts, $threshold, $initialReduction, $unit = 'hours', $logLabel = '')
    {
        $iterationCount = 0;
        $maxIterations = 10;
        $currentReduction = $initialReduction;

        while ($totalContacts > $threshold) {
            if ($iterationCount >= $maxIterations) {
                $currentReduction *= 2;
                $iterationCount = 0;
                Log::warning("Increasing reduction rate for {$logLabel}: now subtracting {$currentReduction} {$unit} per iteration");
            }
            $optimalEndDate = Carbon::parse($optimalEndDate)
                ->sub($unit, $currentReduction)
                ->format('Y-m-d\TH:i:s\Z');
            $totalContacts = $this->countContacts($startDate, $optimalEndDate);
            Log::info("Additional reduction for {$logLabel}: reduced window by {$currentReduction} {$unit}", [
                'newEndDate' => $optimalEndDate,
                'newCount'   => $totalContacts
            ]);
            if (Carbon::parse($optimalEndDate)->lte(Carbon::parse($startDate))) {
                Log::warning("Reached the start date during {$logLabel} reduction");
                break;
            }
            $iterationCount++;
        }
    }

    /**
     * Fetch all contacts in batches
     */
    public function getAllContactsInTimeWindow($startDate, $endDate)
    {
        $contacts = [];
        $after = null;
        $hasMore = true;
        $batchSize = 100; // HubSpot's max page size
        $pageCount = 0;
        $maxPages = 200; // Increased to handle more contacts
        $retrievalWarningLimit = 9000; // Warning threshold
        $maxHubspotApiLimit = 10000; // Actual HubSpot API limit

        Log::info("Starting to fetch contacts", [
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);

        // Continue fetching until no more results or we hit page limit
        while ($hasMore && $pageCount < $maxPages) {
            try {
                // Respect API rate limits
                if ($pageCount > 0) {
                    usleep(100000); // 100ms delay
                }

                $response = $this->searchContacts($startDate, $endDate, $batchSize, $after);

                if (isset($response['results']) && !empty($response['results'])) {
                    $contacts = array_merge($contacts, $response['results']);

                    // Check pagination
                    if (isset($response['paging']) && isset($response['paging']['next']['after'])) {
                        $after = $response['paging']['next']['after'];
                    } else {
                        $hasMore = false;
                    }

                    // Warn when approaching HubSpot's limit
                    if (count($contacts) >= $retrievalWarningLimit && count($contacts) < $maxHubspotApiLimit) {
                        Log::warning("Approaching HubSpot API limit with " . count($contacts) . " contacts retrieved");
                    }

                    // If we've hit HubSpot's API limit, we need to stop and warn
                    if (count($contacts) >= $maxHubspotApiLimit) {
                        Log::warning("Reached HubSpot's API limit of 10,000 contacts. The current time window is too large and should be reduced.");
                        $hasMore = false;
                        break;
                    }
                } else {
                    $hasMore = false;
                }

                $pageCount++;
            } catch (\Exception $e) {
                Log::error("Error fetching contacts", [
                    'error' => $e->getMessage(),
                    'page' => $pageCount
                ]);

                // If we have some contacts, return them instead of failing completely
                if (count($contacts) > 0) {
                    $hasMore = false;
                } else {
                    // If no contacts were retrieved, throw the exception
                    throw $e;
                }
            }
        }

        Log::info("Retrieved " . count($contacts) . " contacts");
        return $contacts;
    }

    /**
     * Get or create a sync status record
     */
    public function getSyncStatus($entityType = 'contacts')
    {
        return HubspotSyncStatus::firstOrCreate(
            ['entity_type' => $entityType],
            [
                'last_sync_timestamp' => null,
                'status' => 'idle',
                'start_window' => '2020-03-01T00:00:00Z', // Default start date
                'end_window' => Carbon::now()->format('Y-m-d\TH:i:s\Z')
            ]
        );
    }

    /**
     * Update sync status
     */
    public function updateSyncStatus($entityType, $data)
    {
        $status = $this->getSyncStatus($entityType);
        $status->update($data);
        return $status;
    }

    public function searchContactsByModifiedDate($startDate, $endDate, $limit = 100, $after = null)
    {
        $url = "{$this->baseUrl}/crm/v3/objects/contacts/search";

        $payload = [
            'filterGroups' => [
                [
                    'filters' => [
                        [
                            'propertyName' => 'lastmodifieddate',
                            'operator' => 'GTE',
                            'value' => $startDate
                        ],
                        [
                            'propertyName' => 'lastmodifieddate',
                            'operator' => 'LT',
                            'value' => $endDate
                        ]
                    ]
                ]
            ],
            'properties' => [
                'firstname',
                'lastname',
                'gender',
                'email',
                'phone',
                'hubspot_owner_id',
                'hs_lead_status',
                'company',
                'lifecyclestage',
                'country',
                'createdate',
                'lastmodifieddate
            '
            ],
            'limit' => $limit,
        ];

        if ($after) {
            $payload['after'] = $after;
        }

        // Set a reasonable timeout to prevent hanging
        $response = Http::timeout(15)->withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
            'Content-Type' => 'application/json',
        ])->post($url, $payload);

        if ($response->failed()) {
            Log::error('HubSpot API error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            throw new \Exception('HubSpot API error: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Count total contacts between date range by modified date
     */
    public function countContactsByModifiedDate($startDate, $endDate)
    {
        $result = $this->searchContactsByModifiedDate($startDate, $endDate, 1);
        return $result['total'] ?? 0;
    }

    /**
     * Find optimal time window based on modified date
     */
    public function findOptimalTimeWindowByModifiedDate($startDate, $endDate)
    {
        $minBatchSize = 3000;  // Minimum contacts to retrieve in a batch
        $maxBatchSize = 10000; // Maximum contacts to retrieve in a batch
        $optimalEndDate = $endDate;

        // Set script timeout to handle large datasets
        if (php_sapi_name() === 'cli') {
            set_time_limit(1500); // 15 minutes
        }

        // First check total contacts in the full time window
        $totalContacts = $this->countContactsByModifiedDate($startDate, $optimalEndDate);

        Log::info("Starting dynamic time window adjustment (by modified date)", [
            'startDate' => $startDate,
            'endDate' => $optimalEndDate,
            'initialCount' => $totalContacts
        ]);

        // Step 1: If too many contacts, reduce end_date by 1 hour until within max limit
        $attempts = 0;
        $maxAttempts = 100; // Safeguard against infinite loops

        while ($totalContacts > $maxBatchSize && $attempts < $maxAttempts) {
            // Reduce end date by 1 hour
            $optimalEndDate = Carbon::parse($optimalEndDate)->subHour()->format('Y-m-d\TH:i:s\Z');

            // Get new count
            $totalContacts = $this->countContactsByModifiedDate($startDate, $optimalEndDate);

            Log::info("Reduced window by 1 hour (modified date)", [
                'newEndDate' => $optimalEndDate,
                'newCount' => $totalContacts,
                'attempt' => $attempts + 1
            ]);

            $attempts++;

            // Check if we're getting too close to start date
            if (Carbon::parse($optimalEndDate)->lte(Carbon::parse($startDate))) {
                Log::warning("End date approaching start date - stopping reduction");
                // Reset to a small buffer after start date
                $optimalEndDate = Carbon::parse($startDate)->addHours(1)->format('Y-m-d\TH:i:s\Z');
                $totalContacts = $this->countContactsByModifiedDate($startDate, $optimalEndDate);
                break;
            }
        }

        // Step 2: If too few contacts, increase end_date by 1 minute until minimum threshold
        $attempts = 0;

        while ($totalContacts < $minBatchSize && $attempts < $maxAttempts) {
            // Add 1 minute to end date
            $optimalEndDate = Carbon::parse($optimalEndDate)->addMinute()->format('Y-m-d\TH:i:s\Z');

            // Don't exceed original end date
            if (Carbon::parse($optimalEndDate)->gt(Carbon::parse($endDate))) {
                $optimalEndDate = $endDate;
                $totalContacts = $this->countContactsByModifiedDate($startDate, $optimalEndDate);
                Log::info("Reached original end date during increase phase (modified date)", [
                    'finalEndDate' => $optimalEndDate,
                    'finalCount' => $totalContacts
                ]);
                break;
            }

            // Get new count
            $totalContacts = $this->countContactsByModifiedDate($startDate, $optimalEndDate);

            Log::info("Increased window by 1 minute (modified date)", [
                'newEndDate' => $optimalEndDate,
                'newCount' => $totalContacts,
                'attempt' => $attempts + 1
            ]);

            $attempts++;
        }

        // Step 3: Fine-tuning if we're over the max batch size
        $attempts = 0;

        while ($totalContacts > $maxBatchSize && $attempts < $maxAttempts) {
            // Reduce by 5 seconds for fine adjustment
            $optimalEndDate = Carbon::parse($optimalEndDate)->subSeconds(3)->format('Y-m-d\TH:i:s\Z');

            // Get new count
            $totalContacts = $this->countContactsByModifiedDate($startDate, $optimalEndDate);

            Log::info("Fine-tuning: reduced window by 3 seconds (modified date)", [
                'newEndDate' => $optimalEndDate,
                'newCount' => $totalContacts,
                'attempt' => $attempts + 1
            ]);

            $attempts++;
        }

        Log::info("Final time window adjustment result (modified date)", [
            'startDate' => $startDate,
            'endDate' => $optimalEndDate,
            'totalContacts' => $totalContacts,
            'isWithinLimits' => ($totalContacts >= $minBatchSize && $totalContacts <= $maxBatchSize) ? 'Yes' : 'No'
        ]);

        return [
            'endDate' => $optimalEndDate,
            'totalContacts' => $totalContacts
        ];
    }

    /**
     * Fetch all contacts in batches by modified date
     */
    public function getAllContactsByModifiedDate($startDate, $endDate)
    {
        $contacts = [];
        $after = null;
        $hasMore = true;
        $batchSize = 100; // HubSpot's max page size
        $pageCount = 0;
        $maxPages = 100; // Limit to prevent infinite loops
        $maxContactsToRetrieve = 9999; // Make sure it's not a multiple of 1000

        Log::info("Starting to fetch contacts by modified date", [
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);

        while ($hasMore && $pageCount < $maxPages && count($contacts) < $maxContactsToRetrieve) {
            try {
                // Respect API rate limits
                if ($pageCount > 0) {
                    usleep(100000); // 100ms delay
                }

                $response = $this->searchContactsByModifiedDate($startDate, $endDate, $batchSize, $after);

                if (isset($response['results']) && !empty($response['results'])) {
                    $contacts = array_merge($contacts, $response['results']);

                    // Check pagination
                    if (isset($response['paging']) && isset($response['paging']['next']['after'])) {
                        $after = $response['paging']['next']['after'];
                    } else {
                        $hasMore = false;
                    }
                } else {
                    $hasMore = false;
                }

                $pageCount++;
            } catch (\Exception $e) {
                Log::error("Error fetching contacts by modified date", [
                    'error' => $e->getMessage(),
                    'page' => $pageCount
                ]);

                // If we have some contacts, return them instead of failing completely
                if (count($contacts) > 0) {
                    $hasMore = false;
                } else {
                    // If no contacts were retrieved, throw the exception
                    throw $e;
                }

                if (count($contacts) >= 9999) {
                    Log::warning("Retrieved a large number of contacts (" . count($contacts) . "). This is close to HubSpot's API limit of 10,000. Some contacts may have been missed.");
                }
            }
        }

        Log::info("Retrieved " . count($contacts) . " contacts by modified date");

        return $contacts;
    }
}
