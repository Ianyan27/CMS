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
        $buhId = $buhId;  // Get BUH ID from the logged-in user

        try {
            // Retrieve all owners under the specified BUH, sorted by owner_pid
            $allOwners = SaleAgent::where('fk_bu_country', function ($query) use ($buhId, $countryId) {
                $query->select('id')
                    ->from('bu_country')
                    ->where('bu_id', $buhId)
                    ->where('country_id', $countryId);
            })->orderBy('id')->get();
            Log::info('Total owners retrieved for BUH ID ' . $buhId . ':', ['count' => $allOwners->count()]);

            if ($allOwners->isEmpty()) {
                throw new \Exception("No sales agents assigned. Please assign the appropriate sales agents. BUH ID: " . $buhId);
            }

            //Retrieve only active owners for assignment purposes
            $activeOwners = $allOwners->where('status', 'active')->values(); // Filter to get only active owners
            Log::info('Active owner PIDs available for assignment:', ['active_owner_pids' => $activeOwners->pluck('owner_pid')->toArray()]);

            if ($activeOwners->isEmpty()) {
                throw new \Exception("No active sales agents available for assignment. BUH ID: " . $buhId);
            }

            // Retrieve unassigned contacts
            $contacts = Contact::whereNull('fk_contacts__owner_pid')->get();
            Log::info('Total unassigned contacts:', ['count' => $contacts->count()]);

            // Get the last assigned contact for the BUH
            $lastAssignedContact = Contact::whereNotNull('fk_contacts__owner_pid')
                ->whereIn('fk_contacts__owner_pid', $allOwners->pluck('owner_pid'))
                ->orderBy('contact_pid', 'desc')
                ->first();

            // Determine the next owner PID to start with
            $nextOwnerPid = $this->determineNextOwnerPid($lastAssignedContact, $activeOwners);
            Log::info('Starting allocation with owner_pid:', ['next_owner_pid' => $nextOwnerPid]);

            // Allocate each unassigned contact
            foreach ($contacts as $contact) {
                // Find the owner with the calculated next owner_pid
                $owner = $activeOwners->firstWhere('owner_pid', $nextOwnerPid);

                if ($owner) {
                    // Assign the contact to the owner
                    $contact->fk_contacts__owner_pid = $owner->owner_pid;
                    $contact->date_of_allocation = Carbon::now();
                    $contact->save();

                    Log::info('Assigned contact to owner:', [
                        'contact_id' => $contact->id,
                        'owner_pid' => $owner->owner_pid,
                    ]);

                    // Update the total assigned contacts count for the owner
                    $owner->total_assign_contacts = Contact::where('fk_contacts__owner_pid', $owner->owner_pid)->count();
                    $owner->save();

                    Log::info('Updated owner details:', [
                        'owner_pid' => $owner->owner_pid,
                        'total_assign_contacts' => $owner->total_assign_contacts,
                    ]);

                    // Get the next owner PID for the next contact
                    $nextOwnerPid = $this->getNextOwnerPid($nextOwnerPid, $activeOwners);
                    Log::info('Next owner_pid calculated:', ['next_owner_pid' => $nextOwnerPid]);
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
        // If there is no previously assigned owner, start with the first owner in the sorted list
        if (!$lastAssignedOwner) {
            $firstOwnerPid = $owners->first()->owner_pid;
            Log::info('No previous owner found. Starting with owner_pid ' . $firstOwnerPid);
            return $firstOwnerPid;
        }

        // Retrieve the last owner PID
        $lastOwnerPid = $lastAssignedOwner->fk_contacts__owner_pid;
        Log::info('Determining next owner_pid based on last owner_pid:', ['last_owner_pid' => $lastOwnerPid]);

        // Use the getNextOwnerPid function to find the next owner PID
        $nextOwnerPid = $this->getNextOwnerPid($lastOwnerPid, $owners);

        Log::info('Next owner_pid determined successfully', [
            'last_owner_pid' => $lastOwnerPid,
            'next_owner_pid' => $nextOwnerPid
        ]);

        return $nextOwnerPid;
    }


    private function getNextOwnerPid($currentOwnerPid, $owners)
    {
        Log::info('Finding next owner PID', [
            'current_owner_pid' => $currentOwnerPid,
            'total_owners_count' => $owners->count(),
            'owner_pids' => $owners->pluck('owner_pid')->toArray()
        ]);

        // Find the index of the current owner_pid in the owners collection
        $currentIndex = $owners->pluck('owner_pid')->search($currentOwnerPid);

        // If the current owner PID is not found, start from the first owner
        if ($currentIndex === false) {
            Log::warning('Current owner PID not found in owners list, starting with the first owner.');
            $currentIndex = -1; // Set to -1 so next index will be 0
        }

        // Calculate the next index using modulo to wrap around the collection
        $nextIndex = ($currentIndex + 1) % $owners->count();

        // Get the next owner PID
        $nextOwnerPid = $owners[$nextIndex]->owner_pid;

        Log::info('Next owner PID calculated successfully', [
            'current_owner_pid' => $currentOwnerPid,
            'next_owner_pid' => $nextOwnerPid,
            'next_index' => $nextIndex
        ]);

        return $nextOwnerPid;
    }
}
