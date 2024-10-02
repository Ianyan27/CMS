<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delete_contacts extends Model
{
    use HasFactory;
    protected $table = 'deleted_contacts';

    // Specify the fields that are mass assignable
    protected $fillable = [
        'fk_engagements__contact_pid',
        'activity_name',
        'date',
        'details',
        'attachments',
    ];
}
