<?php
namespace App\Services;

use App\Models\Contact;
use App\Models\Owner;
use Carbon\Carbon;

class RoundRobinAllocator
{
    public function allocate()
    {
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

            // Re-sort the owners to ensure the one with the least contacts is first
            $owners = $owners->sortBy('contacts_count')->values();
        }
    }
}
