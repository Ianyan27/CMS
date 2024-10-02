<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $table = 'country';

    protected $fillable = ['name'];

    // A country can have many BU Country records
    public function buCountries()
    {
        return $this->hasMany(BuCountry::class, 'country_id');
    }
}
