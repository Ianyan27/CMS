<?php

namespace App\Services;

use App\Models\Contact;
use App\Models\Owner;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class RoundRobinAllocator
{
    public function allocate()
    {
        try {
            // Get the BUH ID from the logged-in user
            $buhId = Auth::user()->id;

            // Retrieve owners (sales agents) under the specified BUH
            $owners = Owner::where('fk_buh', $buhId)->get();
            Log::info('Total owners retrieved for BUH ID ' . $buhId . ':', ['count' => $owners->count()]);

            if ($owners->isEmpty()) {
                throw new \Exception("No owners found for allocation under BUH ID " . $buhId);
            }

            // Get unassigned contacts
            $contacts = Contact::whereNull('fk_contacts__owner_pid')->get();
            Log::info('Total unassigned contacts:', ['count' => $contacts->count()]);

            // Get the last assigned owner_pid for this BUH
            $lastAssignedOwner = Contact::whereNotNull('fk_contacts__owner_pid')
                ->whereIn('fk_contacts__owner_pid', $owners->pluck('owner_pid'))
                ->orderBy('date_of_allocation', 'desc')
                ->first();
            Log::info('Last assigned owner_pid for BUH ID ' . $buhId . ':', ['owner_pid' => $lastAssignedOwner ? $lastAssignedOwner->fk_contacts__owner_pid : 'None']);

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
                    $nextOwnerPid = $this->getNextOwnerPid($nextOwnerPid, $owners);
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
        if (!$lastAssignedOwner) {
            // If no contacts have been assigned yet, start with the first owner in the list
            $firstOwnerPid = $owners->first()->owner_pid;
            Log::info('No previous owner found. Starting with owner_pid ' . $firstOwnerPid);
            return $firstOwnerPid;
        }

        $lastOwnerPid = $lastAssignedOwner->fk_contacts__owner_pid;
        Log::info('Determining next owner_pid based on last owner_pid:', ['last_owner_pid' => $lastOwnerPid]);

        // Determine the next owner_pid based on the last one
        return $this->getNextOwnerPid($lastOwnerPid, $owners);
    }

    private function getNextOwnerPid($currentOwnerPid, $owners)
    {
        // Find the index of the current owner_pid in the owners collection
        $currentIndex = $owners->pluck('owner_pid')->search($currentOwnerPid);

        // Get the next index, and wrap around if necessary
        $nextIndex = ($currentIndex + 1) % $owners->count();

        // Get the next owner_pid
        $nextOwnerPid = $owners[$nextIndex]->owner_pid;
        Log::info('Calculated next owner_pid:', ['currentOwnerPid' => $currentOwnerPid, 'nextOwnerPid' => $nextOwnerPid]);

        return $nextOwnerPid;
    }
}
