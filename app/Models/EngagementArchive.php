<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EngagementArchive extends Model
{
    protected $table = 'engagement_archives';
    protected $primaryKey = 'engagement_archive_pid';

    protected $fillable = [
        'fk_engagement_archives__contact_archive_pid',
        'activity_name',
        'date',
        'details',
        'attachments'
    ];

    public function contactArchive()
    {
        return $this->belongsTo(ContactArchive::class, 'fk_engagement_archives__contact_archive_pid');
    }
}
