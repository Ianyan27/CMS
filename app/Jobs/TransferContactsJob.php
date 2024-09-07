<?php
namespace App\Jobs;

use App\Models\Contact;
use App\Models\TransferContacts;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TransferContactsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $owner_pid;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($owner_pid)
    {
        $this->owner_pid = $owner_pid;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Process contacts in chunks
        Contact::where('fk_contacts__owner_pid', $this->owner_pid)->chunk(100, function ($contacts) {
            foreach ($contacts as $contact) {
                $transferContact = new TransferContacts();
                $transferContact->name = $contact->name;
                $transferContact->email = $contact->email;
                $transferContact->contact_number = $contact->contact_number;
                $transferContact->address = $contact->address;
                $transferContact->country = $contact->country;
                $transferContact->qualification = $contact->qualification;
                $transferContact->job_role = $contact->job_role;
                $transferContact->company_name = $contact->company_name;
                $transferContact->skills = $contact->skills;
                $transferContact->social_profile = $contact->social_profile;
                $transferContact->status = $contact->status;
                $transferContact->source = $contact->source;
                $transferContact->datetime_of_hubspot_sync = $contact->datetime_of_hubspot_sync;
                $transferContact->save();

                // Delete the original contact after transferring
                $contact->delete();
            }
        });
    }
}
