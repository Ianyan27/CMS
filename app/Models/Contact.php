<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use HasFactory;
    protected $table = 'contacts';
    protected $primaryKey = 'contact_pid';

    protected $fillable = [
        'fk_contacts__owner_pid',
        'fk_contacts__sale_agent_id',
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
        return $this->belongsTo(Owner::class, 'fk_contacts__owner_pid');
    }

    public function saleAgent()
    {
        return $this->belongsTo(SaleAgent::class, 'fk_contacts__sale_agent_id');
    }

    public function engagements()
    {
        return $this->hasMany(Engagement::class, 'fk_engagements__contact_pid');
    }
}
