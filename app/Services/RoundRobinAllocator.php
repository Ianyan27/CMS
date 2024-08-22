<?php
namespace App\Services;

use App\Models\Contact;
use App\Models\Owner;
use Carbon\Carbon;

class RoundRobinAllocator
{
    public function allocate()
    {
        // Get all owners from the database
        $owners = Owner::withCount('contacts')->orderBy('contacts_count')->get();
        $ownerCount = $owners->count();

        if ($ownerCount === 0) {
            throw new \Exception("No owners found for allocation.");
        }

        // Get unassigned contacts
        $contacts = Contact::whereNull('fk_contacts__owner_pid')->get();

        $ownerIndex = 0;
        foreach ($contacts as $contact) {
            // Assign each contact to an owner in round-robin fashion, prioritizing owners with fewer contacts
            $contact->fk_contacts__owner_pid = $owners[$ownerIndex]->owner_pid;
            $contact->date_of_allocation = Carbon::now();
            $contact->save();

            // Update the owner index for the next contact
            $ownerIndex = ($ownerIndex + 1) % $ownerCount;

            // Sort owners by the number of contacts they have, ascending
            $owners = $owners->sortBy('contacts_count')->values();

            // Increment the contact count for the assigned owner
            $owners[$ownerIndex]->contacts_count++;
        }
    }
}
