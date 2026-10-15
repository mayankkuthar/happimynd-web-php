<?php

namespace App\Models\Hub;

use Illuminate\Database\Eloquent\Model;

class HubGameProgress extends Model
{
    protected $connection = 'hub';

    protected $table = 'game_progress';

    public $incrementing = false;

    public $timestamps = false;

    // Composite primary key (user_id, game_slug) — always use the query
    // builder for writes; Eloquent does not support composite keys natively.
    protected $primaryKey = 'user_id';

    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'game_slug',
        'data',
        'updated_at',
    ];
}
