<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactDiscard extends Model
{
    protected $table = 'contact_discards';
    protected $primaryKey = 'contact_discard_pid';

    protected $fillable = [
        'fk_contact_discards__owner_pid',
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
        return $this->belongsTo(Owner::class, 'fk_contact_discards__owner_pid');
    }

    public function engagementDiscards()
    {
        return $this->hasMany(EngagementDiscard::class, 'fk_engagement_discards__contact_discard_pid');
    }
}
