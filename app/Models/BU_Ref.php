<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BU_Ref extends Model
{
    protected $table = 'KernelDB_BU_Ref';
    protected $primaryKey = 'bu_id';
    public $timestamps = true;

    protected $fillable = [
        'bu_code', 'bu_desc', 'country_id', 'region',
    ];

    public function country()
    {
        return $this->belongsTo(Country_Ref::class, 'country_id');
    }

    public function employees()
    {
        return $this->hasMany(EmployeeRef::class, 'bu_id');
    }
}
