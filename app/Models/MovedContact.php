<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovedContact extends Model
{
    use HasFactory;

    // Define the table name explicitly if it differs from the model name
    protected $table = 'moved_contacts';

    // Specify the primary key column if it's not 'id'
    protected $primaryKey = 'moved_contact_id';

    // If you are using a non-incrementing or non-numeric primary key
    // public $incrementing = false;
    // protected $keyType = 'string';

    // Specify which attributes should be mass-assignable
    protected $fillable = [
        'fk_contacts__owner_pid',
        'date_of_allocation',
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
        'datetime_of_hubspot_sync',
    ];

    // Define any relationships, accessors, mutators, or other model methods as needed
    // Example: defining a relationship to the Owner model
    public function owner()
    {
        return $this->belongsTo(Owner::class, 'fk_contacts__owner_pid', 'owner_pid');
    }
}
