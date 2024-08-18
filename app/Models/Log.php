<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $table = 'logs';
    protected $primaryKey = 'log_pid';

    protected $fillable = [
        'fk_logs__contact_pid',
        'fk_logs__owner_pid',
        'action_type',
        'action_description',
        'action_timestamp',
        'allocation_date',
        'access_date',
        'activity_datetime'
    ];

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'fk_logs__contact_pid');
    }

    public function owner()
    {
        return $this->belongsTo(Owner::class, 'fk_logs__owner_pid');
    }
}
