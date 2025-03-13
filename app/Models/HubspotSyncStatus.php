<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HubspotSyncStatus extends Model
{
    protected $table = 'hubspot_sync_status';

    use HasFactory;

    protected $fillable = [
        'entity_type',
        'last_sync_timestamp',
        'next_sync_timestamp',
        'last_successful_sync',
        'start_window',
        'end_window',
        'total_synced',
        'total_errors',
        'error_log',
        'status'
    ];

    protected $casts = [
        'last_sync_timestamp' => 'datetime',
        'next_sync_timestamp' => 'datetime',
        'last_successful_sync' => 'datetime',
        'start_window' => 'datetime',
        'end_window' => 'datetime',
    ];
}
