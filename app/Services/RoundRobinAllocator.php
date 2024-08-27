<?php

namespace App\Services;

use App\Models\Contact;
use App\Models\Owner;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class RoundRobinAllocator
{
    public function allocate()
    {
        try {
            // Get all owners from the database
            $owners = Owner::all();
            Log::info('Total owners retrieved:', ['count' => $owners->count()]);

            if ($owners->isEmpty()) {
                throw new \Exception("No owners found for allocation.");
            }

            // Get unassigned contacts
            $contacts = Contact::whereNull('fk_contacts__owner_pid')->get();
            Log::info('Total unassigned contacts:', ['count' => $contacts->count()]);

            // Get the last assigned owner_pid
            $lastAssignedOwner = Contact::whereNotNull('fk_contacts__owner_pid')
                ->orderBy('date_of_allocation', 'desc')
                ->first();
            Log::info('Last assigned owner_pid:', ['owner_pid' => $lastAssignedOwner ? $lastAssignedOwner->fk_contacts__owner_pid : 'None']);

            $nextOwnerPid = $this->determineNextOwnerPid($lastAssignedOwner, $owners);
            Log::info('Starting allocation with owner_pid:', ['next_owner_pid' => $nextOwnerPid]);

            foreach ($contacts as $contact) {
                // Find the owner with the calculated next owner_pid
                $owner = $owners->firstWhere('owner_pid', $nextOwnerPid);

                if ($owner) {
                    // Assign the contact to this owner
                    $contact->fk_contacts__owner_pid = $owner->owner_pid;
                    $contact->date_of_allocation = Carbon::now();
                    $contact->save();

                    Log::info('Assigned contact to owner:', [
                        'contact_id' => $contact->id,
                        'owner_pid' => $owner->owner_pid,
                    ]);

                    // Update the `total_in_progress` count for the assigned owner
                    $owner->total_in_progress = Contact::where('fk_contacts__owner_pid', $owner->owner_pid)->count();
                    $owner->save();

                    // Calculate the next owner_pid for the next iteration
                    $nextOwnerPid = $this->getNextOwnerPid($nextOwnerPid);
                    Log::info('Next owner_pid calculated:', ['next_owner_pid' => $nextOwnerPid]);
                }
            }

            Log::info('Contact allocation process completed successfully.');
        } catch (\Exception $e) {
            Log::error('Allocation error: ' . $e->getMessage());
            throw $e;
        }
    }

    private function determineNextOwnerPid($lastAssignedOwner, $owners)
    {
        if (!$lastAssignedOwner || $lastAssignedOwner->fk_contacts__owner_pid == 1) {
            // If no contacts have been assigned yet, start with owner_pid 1
            Log::info('No previous owner found. Starting with owner_pid 1.');
            return 1;
        }

        $lastOwnerPid = $lastAssignedOwner->fk_contacts__owner_pid;
        Log::info('Determining next owner_pid based on last owner_pid:', ['last_owner_pid' => $lastOwnerPid]);

        // Determine the next owner_pid based on the last one
        return $this->getNextOwnerPid($lastOwnerPid);
    }

    private function getNextOwnerPid($currentOwnerPid)
    {
        // Increment the owner_pid, and wrap it to 1 if it exceeds 10
        $nextOwnerPid = ($currentOwnerPid % 10) + 1;
        Log::info('Calculated next owner_pid:', ['currentOwnerPid' => $currentOwnerPid, 'nextOwnerPid' => $nextOwnerPid]);

        return $nextOwnerPid;
    }
}
