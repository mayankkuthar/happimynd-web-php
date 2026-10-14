<?php

namespace App\Models\Hub;

use Illuminate\Database\Eloquent\Model;

class HubUser extends Model
{
    protected $connection = 'hub';

    protected $table = 'users';

    public $incrementing = false;

    // Timestamps are managed explicitly in UTC by the hub services, never by
    // the app-timezone-aware Eloquent timestamps.
    public $timestamps = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'email',
        'username',
        'name',
        'password_hash',
        'avatar',
        'created_at',
        'updated_at',
    ];

    protected $hidden = [
        'password_hash',
    ];

    public function getAvatarAttribute($value)
    {
        return $value !== null && $value !== '' ? $value : '🧘';
    }
}
