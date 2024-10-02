<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BU extends Model
{
    use HasFactory;

    protected $table = 'bu';

    protected $fillable = ['name'];

    // One BU can have many BU Country records
    public function buCountries()
    {
        return $this->hasMany(BuCountry::class, 'bu_id');
    }
}
