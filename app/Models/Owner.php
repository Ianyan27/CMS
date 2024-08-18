<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Owner extends Model
{
    protected $table = 'owners';
    protected $primaryKey = 'owner_pid';

    protected $fillable = [
        'owner_name',
        'owner_email_id',
        'owner_hubspot_id',
        'owner_business_unit',
        'country',
        'total_in_progress',
        'total_hubspot_sync'
    ];

    public function contacts()
    {
        return $this->hasMany(Contact::class, 'fk_contacts__owner_pid');
    }

    public function contactArchives()
    {
        return $this->hasMany(ContactArchive::class, 'fk_contact_archives__owner_pid');
    }

    public function contactDiscards()
    {
        return $this->hasMany(ContactDiscard::class, 'fk_contact_discards__owner_pid');
    }
}
