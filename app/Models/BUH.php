<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BUH extends Model
{
    use HasFactory;

    protected $table = 'buh';

    protected $fillable = ['name', 'email', 'nationality', 'hubspot_id'];

    // A BUH can have many BU Country records
    public function buCountries()
    {
        return $this->hasMany(BuCountry::class, 'buh_id');
    }
}
