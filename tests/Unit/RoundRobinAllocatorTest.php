<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\RoundRobinAllocator;
use App\Models\Contact;
use App\Models\Owner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;

class RoundRobinAllocatorTest extends TestCase
{
    /**
     * Uncomment this if u want to test and make sure test it in local database
     */
    // use RefreshDatabase;

    /** @test */
    public function it_handles_zero_owners_and_zero_contacts()
    {
        // Arrange: Create a BUH user
        $buhUser = User::create([
            'name' => 'BUH User',
            'email' => 'buh@example.com',
            'password' => bcrypt('password'),
            'role' => 'buh',
        ]);

        // Ensure no Owners or Contacts exist
        $this->assertDatabaseMissing('owners', ['fk_buh' => $buhUser->id]);
        $this->assertDatabaseMissing('contacts', ['fk_contacts__owner_pid' => null]);

        // Mock the authentication
        $this->actingAs($buhUser);

        // Spy on the Log facade to check for error messages
        Log::spy();

        $allocator = new RoundRobinAllocator();

        // Act & Assert: Check that an exception is thrown
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("No sales agent is assigned. Please make sure to assign the appropriate sales agents to continue. BUH ID: " . $buhUser->id);

        $allocator->allocate();
    }

    /** @test */
    public function it_handles_zero_owners_and_one_contact()
    {

        // Arrange: Create a BUH user
        $buhUser = User::create([
            'name' => 'BUH User',
            'email' => 'buh@example.com',
            'password' => bcrypt('password'),
            'role' => 'buh',
        ]);

        // Create a Contact with no owner assigned
        Contact::create([
            'fk_contacts__owner_pid' => null,
            'name' => 'Test Contact',
            'email' => 'contact@example.com',
        ]);

        // Ensure no Owners exist
        $this->assertDatabaseMissing('owners', ['fk_buh' => $buhUser->id]);

        // Mock the authentication
        $this->actingAs($buhUser);

        // Spy on the Log facade to check for error messages
        Log::spy();

        $allocator = new RoundRobinAllocator();

        // Act & Assert: Expect an exception because there are no owners
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("No sales agent is assigned. Please make sure to assign the appropriate sales agents to continue. BUH ID: " . $buhUser->id);

        $allocator->allocate();
    }

    /** @test */
    public function it_handles_one_owner_and_zero_contacts()
    {

        // Arrange: Create 1 owner with a unique `owner_pid`
        $buhUser = User::create([
            'name' => 'BUH User',
            'email' => 'buh' . time() . '@example.com', // Ensure unique email
            'password' => bcrypt('password'),
            'role' => 'buh',
        ]);

        // Create 1 owner with the BUH user
        $owner = Owner::create([
            'owner_name' => 'Owner Name',
            'owner_email_id' => 'owner' . time() . '@example.com', // Ensure unique email
            'country' => 'Malaysia',
            'owner_pid' => 1, // Ensure this matches your database schema and is unique
            'fk_buh' => $buhUser->id,
            'total_assign_contacts' => 0,
        ]);

        // Ensure no contacts are present
        Contact::query()->delete(); // Clear any existing contacts

        // Mock the authentication for the BUH user
        $this->actingAs($buhUser);

        $allocator = new RoundRobinAllocator();
        $allocator->allocate();

        // Act & Assert: Check that no contacts have been assigned
        $this->assertCount(0, Contact::whereNotNull('fk_contacts__owner_pid')->get(), 'No contacts should be assigned if there are no contacts');
    }

    /** @test */
    public function it_allocates_one_contact_to_one_owner()
    {
        // Arrange: Create a BUH user with a unique email
        $uniqueEmail = 'buh' . time() . '@example.com'; // Ensure email is unique

        $buhUser = User::create([
            'name' => 'BUH User',
            'email' => $uniqueEmail,
            'password' => bcrypt('password'),
            'role' => 'buh', // Adjust according to your role attribute
        ]);

        // Create 1 owner with the BUH user
        $owner = Owner::create([
            'owner_name' => 'Owner Name',
            'owner_email_id' => 'owner' . time() . '@example.com', // Ensure unique email
            'country' => 'Malaysia',
            'owner_pid' => 1, // Ensure this matches your database schema and is unique
            'fk_buh' => $buhUser->id,
            'total_assign_contacts' => 0,
        ]);

        // Create 1 contact with no owner assigned
        $contact = Contact::create([
            'fk_contacts__owner_pid' => null,
            'name' => 'Test Contact',
            'email' => 'contact' . time() . '@example.com', // Ensure unique contact email
        ]);

        // Mock the authentication for the BUH user
        $this->actingAs($buhUser);

        $allocator = new RoundRobinAllocator();
        $allocator->allocate();

        // Act & Assert: Check if the contact was assigned to the owner
        $contact = Contact::first();
        $this->assertNotNull($contact->fk_contacts__owner_pid, 'Contact should be assigned to an owner');
        $this->assertEquals($owner->owner_pid, $contact->fk_contacts__owner_pid, 'Contact should be assigned to the first owner');
    }

    /** @test */
    public function it_allocates_one_contact_to_first_owner_when_two_owners()
    {
        // Arrange: Create 2 owners
        $buhUser = User::create([
            'name' => 'BUH User',
            'email' => 'buh' . time() . '@example.com', // Ensure unique email
            'password' => bcrypt('password'),
            'role' => 'buh',
        ]);

        // Create 2 owners
        $owner1 = Owner::create([
            'owner_name' => 'Owner 1',
            'owner_email_id' => 'owner1@example.com', // Ensure unique email
            'country' => 'Malaysia',
            'owner_pid' => 1, // Ensure this matches your database schema and is unique
            'fk_buh' => $buhUser->id,
            'total_assign_contacts' => 0,
        ]);

        $owner2 = Owner::create([
            'owner_name' => 'Owner 2',
            'owner_email_id' => 'owner2@example.com', // Ensure unique email
            'country' => 'Malaysia',
            'owner_pid' => 2, // Ensure this matches your database schema and is unique
            'fk_buh' => $buhUser->id,
            'total_assign_contacts' => 0,
        ]);

        // Create 1 contact with no owner assigned
        $contact = Contact::create([
            'fk_contacts__owner_pid' => null,
            'name' => 'Test Contact',
            'email' => 'contact' . time() . '@example.com', // Ensure unique contact email
        ]);

        // Mock the authentication for the BUH user
        $this->actingAs($buhUser);

        // Act: Run the allocation
        $allocator = new RoundRobinAllocator();
        $allocator->allocate();

        // Assert: Check if the contact was assigned to the first owner
        $contact = Contact::first();
        $this->assertNotNull($contact->fk_contacts__owner_pid, 'Contact should be assigned to an owner');
        $this->assertEquals($owner1->owner_pid, $contact->fk_contacts__owner_pid, 'Contact should be assigned to the first owner');
    }

    /** @test */
    public function it_allocates_two_contacts_to_two_owners_in_round_robin()
    {
        // Arrange: Create a BUH user
        $buhUser = User::create([
            'name' => 'BUH User',
            'email' => 'buh' . time() . '@example.com', // Ensure unique email
            'password' => bcrypt('password'),
            'role' => 'buh',
        ]);

        // Create 2 owners
        $owner1 = Owner::create([
            'owner_name' => 'Owner 1',
            'owner_email_id' => 'owner1@example.com', // Ensure unique email
            'country' => 'Malaysia',
            'owner_pid' => 1, // Ensure this matches your database schema and is unique
            'fk_buh' => $buhUser->id,
            'total_assign_contacts' => 0,
        ]);

        $owner2 = Owner::create([
            'owner_name' => 'Owner 2',
            'owner_email_id' => 'owner2@example.com', // Ensure unique email
            'country' => 'Malaysia',
            'owner_pid' => 2, // Ensure this matches your database schema and is unique
            'fk_buh' => $buhUser->id,
            'total_assign_contacts' => 0,
        ]);

        // Create 2 contacts with no owners assigned
        $contact1 = Contact::create([
            'fk_contacts__owner_pid' => null,
            'name' => 'Test Contact 1',
            'email' => 'contact1' . time() . '@example.com', // Ensure unique contact email
        ]);

        $contact2 = Contact::create([
            'fk_contacts__owner_pid' => null,
            'name' => 'Test Contact 2',
            'email' => 'contact2' . time() . '@example.com', // Ensure unique contact email
        ]);

        // Mock the authentication for the BUH user
        $this->actingAs($buhUser);

        // Act: Run the allocation
        $allocator = new RoundRobinAllocator();
        $allocator->allocate();

        // Assert: Check if the contacts were assigned in a round-robin fashion
        $assignedContacts = Contact::whereNotNull('fk_contacts__owner_pid')->get();
        $this->assertCount(2, $assignedContacts);

        // Extract the owner PIDs assigned to each contact
        $ownerPids = $assignedContacts->pluck('fk_contacts__owner_pid');

        // Assert that the contacts are assigned to different owners
        $this->assertNotEquals(
            $ownerPids[0],
            $ownerPids[1]
        );
    }

    /** @test */
    public function it_allocates_contacts_in_round_robin_when_more_contacts_than_owners()
    {
        // Arrange: Create a BUH user with a specific ID
        $buhUser = User::create([
            'name' => 'BUH User',
            'email' => 'buh' . time() . '@example.com', // Ensure unique email
            'password' => bcrypt('password'),
            'role' => 'buh', // Ensure this matches your role attribute
        ]);

        $buhId = $buhUser->id; // Store BUH ID for later use

        // Arrange: Create 3 owners with the correct BUH reference
        $owner1 = Owner::create([
            'owner_name' => 'Owner 1',
            'owner_email_id' => 'owner1@example.com',
            'country' => 'Malaysia',
            'owner_pid' => 1,
            'fk_buh' => $buhId, // Reference the BUH user ID
            'total_assign_contacts' => 0,
        ]);

        $owner2 = Owner::create([
            'owner_name' => 'Owner 2',
            'owner_email_id' => 'owner2@example.com',
            'country' => 'Malaysia',
            'owner_pid' => 2,
            'fk_buh' => $buhId, // Reference the BUH user ID
            'total_assign_contacts' => 0,
        ]);

        $owner3 = Owner::create([
            'owner_name' => 'Owner 3',
            'owner_email_id' => 'owner3@example.com',
            'country' => 'Malaysia',
            'owner_pid' => 3,
            'fk_buh' => $buhId, // Reference the BUH user ID
            'total_assign_contacts' => 0,
        ]);

        // Arrange: Create 5 contacts with no owners assigned
        $contact1 = Contact::create([
            'fk_contacts__owner_pid' => null,
            'name' => 'Contact 1',
            'email' => 'contact1@example.com',
        ]);

        $contact2 = Contact::create([
            'fk_contacts__owner_pid' => null,
            'name' => 'Contact 2',
            'email' => 'contact2@example.com',
        ]);

        $contact3 = Contact::create([
            'fk_contacts__owner_pid' => null,
            'name' => 'Contact 3',
            'email' => 'contact3@example.com',
        ]);

        $contact4 = Contact::create([
            'fk_contacts__owner_pid' => null,
            'name' => 'Contact 4',
            'email' => 'contact4@example.com',
        ]);

        $contact5 = Contact::create([
            'fk_contacts__owner_pid' => null,
            'name' => 'Contact 5',
            'email' => 'contact5@example.com',
        ]);

        // Mock the authentication for the BUH user
        $this->actingAs($buhUser);

        // Act: Run the allocation
        $allocator = new RoundRobinAllocator();
        $allocator->allocate();

        // Assert: Check if all contacts have been assigned an owner
        $assignedContacts = Contact::whereNotNull('fk_contacts__owner_pid')->get();
        $this->assertCount(5, $assignedContacts, 'All contacts should be assigned an owner.');

        // Retrieve the owners for assertion
        $owners = Owner::orderBy('owner_pid')->get();

        // Check the distribution of contacts
        $this->assertEquals(2, $assignedContacts->where('fk_contacts__owner_pid', $owners[0]->owner_pid)->count(), 'The first owner should have 2 contacts.');
        $this->assertEquals(2, $assignedContacts->where('fk_contacts__owner_pid', $owners[1]->owner_pid)->count(), 'The second owner should have 2 contacts.');
        $this->assertEquals(1, $assignedContacts->where('fk_contacts__owner_pid', $owners[2]->owner_pid)->count(), 'The third owner should have 1 contact.');
    }
}
