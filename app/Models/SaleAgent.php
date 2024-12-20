<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleAgent extends Model
{
    use HasFactory;

    protected $table = 'sale_agent';

    protected $fillable = [
        'name',
        'email',
        'hubspot_id',
        'business_unit',
        'nationality',
        'total_hubspot_sync',
        'business_unit',
        'total_in_progress',
        'total_assign_contacts',
        'total_archive_contacts',
        'total_discard_contacts',
        'bu_country_id'
    ];

    // Sale Agent belongs to a single BU Country
    public function buCountryBuh()
    {
        return $this->belongsTo(BuCountryBUH::class, 'bu_country_id');
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class, 'fk_contacts__sale_agent_id');
    }
}
