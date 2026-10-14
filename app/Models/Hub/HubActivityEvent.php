<?php

namespace App\Models\Hub;

use Illuminate\Database\Eloquent\Model;

class HubActivityEvent extends Model
{
    protected $connection = 'hub';

    protected $table = 'activity_events';

    public $incrementing = false;

    public $timestamps = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'event_type',
        'game_slug',
        'user_type',
        'user_id',
        'created_at',
    ];
}
