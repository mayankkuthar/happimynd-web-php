<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'status'
    ];

    /**
     * each user profile has many users associated with it
     *
     * @return relation
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function batch()
    {
        return $this->hasMany(Batch::class);
    }

    public function batchCategory()
    {
        return $this->hasManyThrough(BatchCategory::class, Batch::class);
    }

    public function getNameAttribute()
    {
        return ucwords($this->attributes['name']);
    }
}
