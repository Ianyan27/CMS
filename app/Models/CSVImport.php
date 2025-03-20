<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CSVImport extends Model
{
    use HasFactory;

    protected $table = 'csv_imports';

    protected $fillable = [
        'file_name',
        'file_content',
        'user_id',
    ];
}
