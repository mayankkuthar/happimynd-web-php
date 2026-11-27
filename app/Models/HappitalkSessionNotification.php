<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HappitalkSessionNotification extends Model
{
    protected $table = 'happitalk_session_notifications';

    protected $fillable = ['happitalk_session_id', 'kind', 'session_starts_at', 'recipient', 'channel', 'status', 'detail', 'admin_id'];

    protected $casts = ['session_starts_at' => 'datetime'];

    public function session()
    {
        return $this->belongsTo(HappitalkSession::class, 'happitalk_session_id');
    }
}
