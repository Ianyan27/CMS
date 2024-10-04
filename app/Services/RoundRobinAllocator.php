<?php

namespace App\Services;

use App\Models\Contact;
use App\Models\Owner;
use App\Models\SaleAgent;
use App\Models\TransferContacts;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class RoundRobinAllocator
{

    public function allocate($countryId, $buhId)
    {

        try {
            // Retrieve all sales agents under the specified BUH, sorted by id
            $allOwners = SaleAgent::where('bu_country_id', function ($query) use ($buhId, $countryId) {
                $query->select('id')
                    ->from('bu_country')
                    ->where('bu_id', $buhId)
                    ->where('country_id', $countryId);
            })->orderBy('id')->get();
            Log::info('Total owners retrieved for BUH ID ' . $buhId . ':', ['count' => $allOwners->count()]);

            if ($allOwners->isEmpty()) {
                throw new \Exception("No sales agents assigned. Please assign the appropriate sales agents. BUH ID: " . $buhId);
            }

            //Retrieve only active sales agents for assignment purposes
            $activeOwners = $allOwners->where('status', 'active')->values(); // Filter to get only active sales agents
            Log::info('Active sale agent IDs available for assignment:', ['active_sale_agent_ids' => $activeOwners->pluck('id')->toArray()]);

            if ($activeOwners->isEmpty()) {
                throw new \Exception("No active sales agents available for assignment. BUH ID: " . $buhId);
            }

            // Retrieve unassigned contacts
            $contacts = Contact::whereNull('fk_contacts__sale_agent_id')->get();
            Log::info('Total unassigned contacts:', ['count' => $contacts->count()]);

            // Get the last assigned contact for the BUH
            $lastAssignedContact = Contact::whereNotNull('fk_contacts__sale_agent_id')
                ->whereIn('fk_contacts__sale_agent_id', $allOwners->pluck('id'))
                ->orderBy('contact_pid', 'desc')
                ->first();

            // Determine the next sale agent ID to start with
            $nextOwnerPid = $this->determineNextOwnerPid($lastAssignedContact, $activeOwners);
            Log::info('Starting allocation with sale_agent_id:', ['next_sale_agent_id' => $nextOwnerPid]);

            // Allocate each unassigned contact
            foreach ($contacts as $contact) {
                // Find the sales agent with the calculated next sale_agent_id
                $owner = $activeOwners->firstWhere('id', $nextOwnerPid);

                if ($owner) {
                    // Assign the contact to the sales agent
                    $contact->fk_contacts__sale_agent_id = $owner->id;
                    $contact->date_of_allocation = Carbon::now();
                    $contact->save();

                    Log::info('Assigned contact to sales agent:', [
                        'contact_id' => $contact->id,
                        'sale_agent_id' => $owner->id,
                    ]);

                    // Update the total assigned contacts count for the sales agent
                    $owner->total_assign_contacts = Contact::where('fk_contacts__sale_agent_id', $owner->id)->count();
                    $owner->save();

                    Log::info('Updated sales agent details:', [
                        'sale_agent_id' => $owner->id,
                        'total_assign_contacts' => $owner->total_assign_contacts,
                    ]);

                    // Get the next sale agent ID for the next contact
                    $nextOwnerPid = $this->getNextOwnerPid($nextOwnerPid, $activeOwners);
                    Log::info('Next sale_agent_id calculated:', ['next_sale_agent_id' => $nextOwnerPid]);
                }
            }

            Log::info('Contact allocation process completed successfully for BUH ID ' . $buhId);
            Log::info("\n");
        } catch (\Exception $e) {
            Log::error('Allocation error for BUH ID ' . $buhId . ': ' . $e->getMessage());
            throw $e;
        }
    }


    private function determineNextOwnerPid($lastAssignedOwner, $owners)
    {
        // If there is no previously assigned sales agent, start with the first sales agent in the sorted list
        if (!$lastAssignedOwner) {
            $firstOwnerPid = $owners->first()->id;
            Log::info('No previous sales agent found. Starting with sale_agent_id ' . $firstOwnerPid);
            return $firstOwnerPid;
        }

        // Retrieve the last sale agent ID
        $lastOwnerPid = $lastAssignedOwner->fk_contacts__sale_agent_id;
        Log::info('Determining next sale_agent_id based on last sale_agent_id:', ['last_sale_agent_id' => $lastOwnerPid]);

        // Use the getNextOwnerPid function to find the next sale agent ID
        $nextOwnerPid = $this->getNextOwnerPid($lastOwnerPid, $owners);

        Log::info('Next sale_agent_id determined successfully', [
            'last_sale_agent_id' => $lastOwnerPid,
            'next_sale_agent_id' => $nextOwnerPid
        ]);

        return $nextOwnerPid;
    }


    private function getNextOwnerPid($currentOwnerPid, $owners)
    {
        Log::info('Finding next sale_agent_id', [
            'current_sale_agent_id' => $currentOwnerPid,
            'total_owners_count' => $owners->count(),
            'sale_agent_ids' => $owners->pluck('id')->toArray()
        ]);

        // Find the index of the current sale_agent_id in the owners collection
        $currentIndex = $owners->pluck('id')->search($currentOwnerPid);

        // If the current sale_agent ID is not found, start from the first sales agent
        if ($currentIndex === false) {
            Log::warning('Current sale_agent_id not found in sales agent list, starting with the first sales agent.');
            $currentIndex = -1; // Set to -1 so next index will be 0
        }

        // Calculate the next index using modulo to wrap around the collection
        $nextIndex = ($currentIndex + 1) % $owners->count();

        // Get the next sale_agent ID
        $nextOwnerPid = $owners[$nextIndex]->id;

        Log::info('Next sale_agent_id calculated successfully', [
            'current_sale_agent_id' => $currentOwnerPid,
            'next_sale_agent_id' => $nextOwnerPid,
            'next_index' => $nextIndex
        ]);

        return $nextOwnerPid;
    }
}
