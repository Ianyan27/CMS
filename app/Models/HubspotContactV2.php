<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HubspotContactV2 extends Model
{
    protected $table = 'hubspot_contacts_v2';

    public $timestamps = false;

    protected $fillable = [
        'hubspot_id',
        'country',
        'country_from',
        'business_unit',
        'ad_channel',
        'your_specialization',
        'campaign_group',
    ];
}
