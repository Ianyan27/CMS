<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArchiveActivities extends Model
{
    use HasFactory;
    protected $table = 'archive_activities';

    // Specify the fields that are mass assignable
    protected $fillable = [
        'fk_engagements__contact_pid',
        'activity_name',
        'date',
        'details',
        'attachments',
    ];
}
