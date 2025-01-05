<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_language',
        'title',
        'message',
        'user_type',
        'scheduled_date_time',
        'is_notification_delivered',
    ];
}
