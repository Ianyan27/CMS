<?php 

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferContacts extends Model{

    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'contact_number',
        'address',
        'country',
        'qualification',
        'job_role',
        'company_name',
        'skills',
        'social_profile',
        'status',
        'source',
        'datetime_of_hubspot_sync'
    ];

}