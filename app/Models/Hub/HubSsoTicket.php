<?php

namespace App\Models\Hub;

use Illuminate\Database\Eloquent\Model;

class HubSsoTicket extends Model
{
    protected $connection = 'hub';

    protected $table = 'sso_tickets';

    public $incrementing = false;

    public $timestamps = false;

    protected $primaryKey = 'code';

    protected $keyType = 'string';

    protected $fillable = [
        'code',
        'user_id',
        'game_id',
        'expires_at',
        'used_at',
        'created_at',
    ];
}
