<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactActivitiesStatus extends Model
{
    protected $table = 'SalesDB_Contact_Activities_Status';
    protected $primaryKey = 'contact_activities_status_id';
    public $timestamps = true;

    protected $fillable = [
        'last_messaging_date', 
        'last_messaging_contents', 
        'last_campaign_date', 
        'contact_id',
        'last_campaign_contents', 
        'last_digital_conversation_date', 
        'digital_conversation_contents',
        'campaign_engagement_score', 
        'messaging_engagement_score', 
        'messaging_sentiment_score',
        'conversation_engagement_score', 
        'leads_score', 
        'leads_score_summary',
        'notes_last_updated'
    ];

    public function contactProfile() {
        return $this->belongsTo(ContactProfile::class, 'contact_id');
    }
}
