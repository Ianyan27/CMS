<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Engagement extends Model
{
    protected $table = 'engagements';
    protected $primaryKey = 'engagement_pid';

    protected $fillable = [
        'fk_engagements__contact_pid',
        'activity_name',
        'date',
        'details',
        'attachments'
    ];

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'fk_engagements__contact_pid');
    }
}
