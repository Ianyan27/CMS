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
            // Get all owners from the database, along with their contact count
            $owners = Owner::withCount('contacts')->orderBy('contacts_count')->get();
            $ownerCount = $owners->count();

            if ($ownerCount === 0) {
                throw new \Exception("No owners found for allocation.");
            }

            // Get unassigned contacts
            $contacts = Contact::whereNull('fk_contacts__owner_pid')->get();

            foreach ($contacts as $contact) {
                // Get the owner with the least number of contacts
                $owner = $owners->first();

                // Assign the contact to this owner
                $contact->fk_contacts__owner_pid = $owner->owner_pid;
                $contact->date_of_allocation = Carbon::now();
                $contact->save();

                // Increment the contact count for the assigned owner
                $owner->contacts_count++;
                $owner->total_in_progress++; // Increment total_in_progress
                $owner->save(); // Save the owner with updated total_in_progress

                // Re-sort the owners to ensure the one with the least contacts is first
                $owners = $owners->sortBy('contacts_count')->values();
            }
        } catch (\Exception $e) {
            Log::error('Allocation error: ' . $e->getMessage());
            throw $e; // Rethrow the exception to let Laravel handle it
        }
    }
}
