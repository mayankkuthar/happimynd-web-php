<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Batch extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'batches';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'user_profile_id'
    ];

    public function scopeWithAndWhereHas($query, $relation, $constraint)
    {
        return $query->whereHas($relation, $constraint)
            ->with([$relation => $constraint]);
    }

    public function userProfile()
    {
        return $this->belongsTo(UserProfile::class);
    }

    public function batchCategory()
    {
        return $this->hasMany(BatchCategory::class)->orderBy('sort_order');
    }

    public function questions()
    {
        return $this->hasManyThrough(Question::class, BatchCategory::class);
    }

    public function reportCharacteristic()
    {
        return $this->hasManyThrough(ReportCharacteristic::class, BatchCategory::class);
    }

    public function assessment()
    {
        return $this->hasMany(Assessment::class);
    }
}
