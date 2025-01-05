<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'result',
        'score',
        'smoothness',
        'liveliness',
        'control',
        'energy_range',
        'clarity',
        'crispness',
        'speech_rate',
        'pause_duration',
        'inferred_at',
    ];

    /**
     * Associated user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
