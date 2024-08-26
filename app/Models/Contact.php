<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $table = 'contacts';
    protected $primaryKey = 'contact_pid';

    protected $fillable = [
        'fk_contacts__owner_pid',
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
        'contact_aging',
        'datetime_of_hubspot_sync'
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class, 'fk_contacts__owner_pid');
    }

    public function engagements()
    {
        return $this->hasMany(Engagement::class, 'fk_engagements__contact_pid');
    }
}
