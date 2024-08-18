<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactArchive extends Model
{
    protected $table = 'contact_archives';
    protected $primaryKey = 'contact_archive_pid';

    protected $fillable = [
        'fk_contact_archives__owner_pid',
        'date_of_allocation',
        'name',
        'email',
        'contact_number',
        'address',
        'country',
        'qualification',
        'job_role',
        'company_name',
        'skills',
        'social_profile',
        'status',
        'source',
        'datetime_of_hubspot_sync'
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class, 'fk_contact_archives__owner_pid');
    }

    public function engagementArchives()
    {
        return $this->hasMany(EngagementArchive::class, 'fk_engagement_archives__contact_archive_pid');
    }
}
