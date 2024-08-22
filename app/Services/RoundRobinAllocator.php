<?php

namespace App\Services;

use App\Models\Contact;
use App\Models\Owner;

class RoundRobinAllocator
{
    public function allocate()
    {
        // Get all owners from the database
        $owners = Owner::all();
        $ownerCount = $owners->count();

        if ($ownerCount === 0) {
            throw new \Exception("No owners found for allocation.");
        }

        // Get unassigned contacts
        $contacts = Contact::whereNull('owner_id')->get();

        $i = 0;
        foreach ($contacts as $contact) {
            // Assign each contact to an owner in round-robin fashion
            $contact->owner_id = $owners[$i % $ownerCount]->id;
            $contact->save();
            $i++;
        }
    }
}
