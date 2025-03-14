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
            'properties' => ['firstname', 'lastname', 'gender', 'email', 'createdate', 'lastmodifieddate'],
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
        $minBatchSize = 3000;  // Minimum contacts to retrieve in a batch
        $maxBatchSize = 9999; // Maximum contacts to retrieve in a batch
        $optimalEndDate = $endDate;

        // Set script timeout to handle large datasets
        if (php_sapi_name() === 'cli') {
            set_time_limit(1500); // 15 minutes
        }

        // First check total contacts in the full time window
        $totalContacts = $this->countContacts($startDate, $optimalEndDate);

        Log::info("Starting adaptive time window adjustment", [
            'startDate' => $startDate,
            'endDate' => $optimalEndDate,
            'initialCount' => $totalContacts
        ]);

        // Step 1: If too many contacts, reduce window by 1 hour until within max limit
        while ($totalContacts > $maxBatchSize) {
            // Reduce end date by 1 hour
            $optimalEndDate = Carbon::parse($optimalEndDate)->subHour()->format('Y-m-d\TH:i:s\Z');

            // Get new count
            $totalContacts = $this->countContacts($startDate, $optimalEndDate);

            Log::info("Reduced window by 1 hour", [
                'newEndDate' => $optimalEndDate,
                'newCount' => $totalContacts
            ]);

            // Check if we're getting too close to start date
            if (Carbon::parse($optimalEndDate)->lte(Carbon::parse($startDate)->addDay())) {
                Log::warning("End date approaching start date - resetting to buffer period");
                // Reset to a small buffer after start date
                $optimalEndDate = Carbon::parse($startDate)->addDays(1)->format('Y-m-d\TH:i:s\Z');
                $totalContacts = $this->countContacts($startDate, $optimalEndDate);
                break;
            }
        }

        // Step 2: If too few contacts, increase window by 1 minute until minimum threshold
        while ($totalContacts < $minBatchSize) {
            // Add 1 minute to end date
            $optimalEndDate = Carbon::parse($optimalEndDate)->addMinute()->format('Y-m-d\TH:i:s\Z');

            // Don't exceed original end date
            if (Carbon::parse($optimalEndDate)->gt(Carbon::parse($endDate))) {
                $optimalEndDate = $endDate;
                $totalContacts = $this->countContacts($startDate, $optimalEndDate);
                Log::info("Reached original end date during increase phase", [
                    'finalEndDate' => $optimalEndDate,
                    'finalCount' => $totalContacts
                ]);
                break;
            }

            // Get new count
            $totalContacts = $this->countContacts($startDate, $optimalEndDate);

            Log::info("Increased window by 1 minute", [
                'newEndDate' => $optimalEndDate,
                'newCount' => $totalContacts
            ]);
        }

        // Step 3: Fine-tuning if we're over the max batch size
        while ($totalContacts > $maxBatchSize) {
            // Reduce by 5 seconds for fine adjustment
            $optimalEndDate = Carbon::parse($optimalEndDate)->subSeconds(5)->format('Y-m-d\TH:i:s\Z');

            // Get new count
            $totalContacts = $this->countContacts($startDate, $optimalEndDate);

            Log::info("Fine-tuning: reduced window by 5 seconds", [
                'newEndDate' => $optimalEndDate,
                'newCount' => $totalContacts
            ]);
        }

        Log::info("Final time window adjustment result", [
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
            'properties' => ['firstname', 'lastname', 'gender', 'email', 'createdate', 'lastmodifieddate'],
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
            $optimalEndDate = Carbon::parse($optimalEndDate)->subSeconds(5)->format('Y-m-d\TH:i:s\Z');

            // Get new count
            $totalContacts = $this->countContactsByModifiedDate($startDate, $optimalEndDate);

            Log::info("Fine-tuning: reduced window by 5 seconds (modified date)", [
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
