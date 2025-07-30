<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeRef extends Model
{
    protected $table = 'KernelDB_Employee_Ref';
    protected $primaryKey = 'employee_id';
    public $timestamps = true;

    protected $fillable = [
        'owner_id', 'employee_name', 'supervisor', 'bu_id',
    ];

    public function businessUnit()
    {
        return $this->belongsTo(BU_Ref::class, 'bu_id');
    }
}
