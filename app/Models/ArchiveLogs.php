<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArchiveLogs extends Model
{
    use HasFactory;

    protected $table ='archive__logs';

    protected $primaryKey = 'archive_log_pid';

    protected $fillable = [
        'fk_logs__archive_contact_pid',
        'fk_logs__owner_pid',
        'action_type',
        'action_description',
        'action_timestamp',
        'allocation_date',
        'access_date',
        'activity_datetime'
    ];

    public function contactArchive()
    {
        return $this->belongsTo(ContactArchive::class, 'fk_logs__archive_contact_pid');
    }

}
