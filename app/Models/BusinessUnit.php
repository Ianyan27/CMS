<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessUnit extends Model
{
    use HasFactory;

    // Specify the table name if it differs from the plural form of the model
    protected $table = 'Business_Unit';

    // Define the primary key column
    protected $primaryKey = 'bu_id';

    // If the primary key is not auto-incrementing, specify this
    public $incrementing = true;

    // If you do not have timestamps in the table, disable them
    public $timestamps = true;

    // Define the fillable attributes (attributes that are mass assignable)
    protected $fillable = [
        'business_unit',
        'country',
        'BUH'
    ];
}
