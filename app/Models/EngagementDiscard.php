<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EngagementDiscard extends Model
{
    protected $table = 'engagement_discards';
    protected $primaryKey = 'engagement_discard_pid';

    protected $fillable = [
        'fk_engagement_discards__contact_discard_pid',
        'activity_name',
        'date',
        'details',
        'attachments'
    ];

    public function contactDiscard()
    {
        return $this->belongsTo(ContactDiscard::class, 'fk_engagement_discards__contact_discard_pid');
    }
}
