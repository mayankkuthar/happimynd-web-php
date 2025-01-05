<?php

namespace App\Models;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Assessment extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'assessments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'started_at',
        'ended_at',
        'report',
        'mobile',
        'batch_id',
        'platform',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $dates = ['started_at', 'ended_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approve()
    {
        return $this->hasOne(AssessmentApprove::class);
    }

    public function score()
    {
        return $this->hasOne(AssessmentScore::class);
    }

    public function scopeCompletedAssessment($query)
    {
        return $query->whereNotNull('started_at')->whereNotNull('ended_at');
    }

    public function completedAssessment()
    {
        return $this->whereNotNull('assessments.ended_at');
    }

    public function expiryDays()
    {
        $no_of_days = Carbon::parse($this->ended_at)->diffInDays(Carbon::parse($this->ended_at)->addMonths(3));
        $no_of_weeks = (int)ceil($no_of_days / 7);
        if ($no_of_weeks > 0) {
            return $no_of_weeks . ' weeks left';
        } else {
            return $no_of_days . ' days left';
        }
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function answer()
    {
        return $this->hasMany(AssessmentAnswer::class);
    }

    public function isCompleted()
    {
        return !empty($this->ended_at);
    }
}
