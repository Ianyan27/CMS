<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\Contact;
use App\Models\User;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_update_contact_returns_successful_response(){

        $user = User::factory()->create(['role' => 'Admin']);

        $contact = Contact::factory()->create();

        $response = $this->actingAs($user)->post(route('contact#update-contact', [
            'contact_pid' => $contact->contact_pid,
            'id' => $user->id
        ]), [
            'name' => 'lebron',
            'email' => 'lebron@lithan.com',
            'contact_number' => '123456789',
            'address' => 'new_address',
            'country' => 'New Zealand',
            'qualification' => 'none',
            'job_role' => 'none',
            'skills' => 'updated_skills', 
            'status' => 'none'
        ]);
        $response->assertStatus(302);    
        // Verify that the session has the expected error message
        $response->assertSessionHas('error', 'Admin cannot edit the contact information');
    }

    public function test_add_new_user(){
        $user = User::factory()->create(['role' => 'Admin']);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('user#save-user'),[
            'name' => 'Testing Names',
            'email' => 'testing@gmail.com',
            
        ]);
        

        $response->assertRedirect('/');
        $response->assertSessionHas('error', 'BUH cannot add new users');
    }
}
