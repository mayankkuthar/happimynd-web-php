<?php

namespace App\Models\Hub;

use Illuminate\Database\Eloquent\Model;

class HubGameSession extends Model
{
    protected $connection = 'hub';

    protected $table = 'game_sessions';

    public $incrementing = false;

    public $timestamps = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'game_slug',
        'user_type',
        'user_id',
        'started_at',
        'ended_at',
        'duration_sec',
        'ended_by',
        'stats',
    ];
}
