<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactEngagementStatus extends Model
{
    protected $table = 'SalesDB_Contact_Engagement_Status';
    protected $primaryKey = 'contact_engagement_status_id';
    public $timestamps = true;

    protected $fillable = [
        'contact_mgr',
        'contact_exec',
        'contact_status',
        'cilos_status',
        'cilos_stage',
        'cilos_substage',
        'win_lost_reasons',
        'proposed_solution',
        'product_interest',
        'contact_id',
        'lead_status'
    ];

    public function contactProfile() {
        return $this->belongsTo(ContactProfile::class, 'contact_id');
    }
}
