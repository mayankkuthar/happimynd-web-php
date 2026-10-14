<?php

namespace App\Models\Hub;

use Illuminate\Database\Eloquent\Model;

class HubGameContent extends Model
{
    protected $connection = 'hub';

    protected $table = 'game_content';

    public $incrementing = false;

    public $timestamps = false;

    // Composite primary key (game_slug, collection) — always use the query
    // builder for writes; Eloquent does not support composite keys natively.
    protected $primaryKey = 'game_slug';

    protected $keyType = 'string';

    protected $fillable = [
        'game_slug',
        'collection',
        'items',
        'updated_at',
    ];
}
