<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country_Ref extends Model
{
    protected $table = 'KernelDB_Country_Ref';
    protected $primaryKey = 'country_id';
    public $timestamps = true;

    protected $fillable = [
        'country_code', 'country_desc',
    ];

    public function businessUnits()
    {
        return $this->hasMany(BU_Ref::class, 'country_id');
    }
}
