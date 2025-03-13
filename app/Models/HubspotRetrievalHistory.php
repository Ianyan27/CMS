<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HubspotRetrievalHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'retrieved_count',
        'start_date',
        'end_date'
    ];
    
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];
}
