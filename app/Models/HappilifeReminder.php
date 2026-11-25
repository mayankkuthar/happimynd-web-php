<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HappilifeReminder extends Model
{
    const SOURCE_AUTO = 'auto';
    const SOURCE_MANUAL = 'manual';

    protected $table = 'happilife_reminders';

    protected $fillable = [
        'user_id',
        'assessment_id',
        'source',
        'stage',
        'channels',
        'answered',
        'total',
        'message',
        'sent_by_admin_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }
}
