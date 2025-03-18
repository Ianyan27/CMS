<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HubspotContacts extends Model
{
    protected $primaryKey = 'contact_id';
    public $incrementing = false;

    protected $fillable = [
        'contact_id',
        'firstname',
        'lastname',
        'email',
        'gender',
        'delete_flag',
        'hubspot_lastmodified',
        'marked_deleted',

        // Newly added columns
        'phone',
        'hubspot_owner_id',
        'hs_lead_status',
        'company',
        'lifecyclestage',
        'country',
    ];
}
