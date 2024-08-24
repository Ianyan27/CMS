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
            $ownerCount = $owners->count();

            if ($ownerCount === 0) {
                throw new \Exception("No owners found for allocation.");
            }

            // Get unassigned contacts
            $contacts = Contact::whereNull('fk_contacts__owner_pid')->get();

            // Determine direction based on the last 5 assigned contacts
            $lastContacts = Contact::whereNotNull('fk_contacts__owner_pid')
                ->limit(5)
                ->pluck('fk_contacts__owner_pid');

            $forward = $this->determineDirection($lastContacts, $ownerCount);

            // Initialize ownerIndex based on direction
            $ownerIndex = $forward ? 0 : $ownerCount - 1;

            foreach ($contacts as $contact) {
                // Get the owner based on the current direction
                $owner = $owners[$ownerIndex];

                // Assign the contact to this owner
                $contact->fk_contacts__owner_pid = $owner->owner_pid;
                $contact->date_of_allocation = Carbon::now();
                $contact->save();

                // Update the `total_in_progress` count for the assigned owner
                $owner->total_in_progress = Contact::where('fk_contacts__owner_pid', $owner->owner_pid)->count();
                $owner->save();

                // Update the owner index based on the direction
                if ($forward) {
                    $ownerIndex++;
                    if ($ownerIndex >= $ownerCount) {
                        $ownerIndex = $ownerCount - 1;
                        $forward = false; // Change direction to backward
                    }
                } else {
                    $ownerIndex--;
                    if ($ownerIndex < 0) {
                        $ownerIndex = 0;
                        $forward = true; // Change direction to forward
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Allocation error: ' . $e->getMessage());
            throw $e; // Rethrow the exception to let Laravel handle it
        }
    }

    private function determineDirection($lastContacts, $ownerCount)
    {
        // Log the last 5 contacts that were assigned
        Log::debug('Last 5 assigned contacts:', $lastContacts->toArray());

        if ($lastContacts->isEmpty()) {
            Log::debug('No previous contacts found. Defaulting to forward direction.');
            // If no previous contacts are found, default to forward
            return true;
        }

        $ascending = $lastContacts->every(function ($value, $key) use ($lastContacts) {
            return $key === 0 || $value >= $lastContacts[$key - 1];
        });

        $descending = $lastContacts->every(function ($value, $key) use ($lastContacts) {
            return $key === 0 || $value <= $lastContacts[$key - 1];
        });

        // Log the results of the ascending and descending checks
        Log::debug('Ascending check result:', ['ascending' => $ascending]);
        Log::debug('Descending check result:', ['descending' => $descending]);

        // If previous assignments were ascending, we should reverse direction
        if ($ascending) {
            Log::debug('Contacts were assigned in ascending order. Changing direction to backward.');
            return false; // Start from the last owner and go backward
        }

        // If previous assignments were descending, we should reverse direction
        if ($descending) {
            Log::debug('Contacts were assigned in descending order. Changing direction to forward.');
            return true; // Start from the first owner and go forward
        }

        // If neither, maintain current direction based on the last known direction
        Log::debug('Direction is unclear. Defaulting to forward.');
        return true; // Default to forward if direction is unclear
    }
}
