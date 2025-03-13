<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HubspotContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'hubspot_id',
        'email',
        'firstname',
        'lastname',
        'gender',
        'hubspot_created_at',
        'hubspot_updated_at'
    ];

    protected $casts = [
        'hubspot_created_at' => 'datetime',
        'hubspot_updated_at' => 'datetime',
    ];
}
