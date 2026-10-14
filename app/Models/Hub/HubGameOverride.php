<?php

namespace App\Models\Hub;

use Illuminate\Database\Eloquent\Model;

class HubGameOverride extends Model
{
    protected $connection = 'hub';

    protected $table = 'game_overrides';

    public $incrementing = false;

    public $timestamps = false;

    protected $primaryKey = 'slug';

    protected $keyType = 'string';

    protected $fillable = [
        'slug',
        'name',
        'tagline',
        'description',
        'hidden',
        'updated_at',
    ];

    protected $casts = [
        'hidden' => 'boolean',
    ];
}
