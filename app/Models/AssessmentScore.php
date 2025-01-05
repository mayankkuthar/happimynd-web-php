<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssessmentScore extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'assessment_id',
        'attempts',
        'anxiety_score',
        'depression_score',
        'stress_score',
        'burn_out_score',
        'happiness_score',
        'internet_addiction_score',
        'self_esteem_score',
        'resilience_score',
        'job_satisfaction_score',
        'anxiety_level',
        'depression_level',
        'stress_level',
        'burn_out_level',
        'happiness_level',
        'internet_addiction_level',
        'self_esteem_level',
        'resilience_level',
        'job_satisfaction_level',
        'personality_1',
        'personality_2',
        'personality_score_1',
        'personality_score_2',
        'scores'
    ];

    protected $casts = [
        'scores' => "array"
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    public function setScoresAttribute($value)
    {
        $this->attributes['scores'] = json_encode($value);
    }
}
