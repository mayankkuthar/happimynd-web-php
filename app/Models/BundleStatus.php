<?php

namespace App\Models;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BundleStatus extends Model
{
    use HasFactory, SoftDeletes;


    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bundle_statuses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'valid',
        'percentage_covered',
        'user_id',
        'plan_id',
        'receipt_id',
        'expires_at'
    ];
    protected $casts = [
        'valid' => 'boolean',
        'expires_at' => 'datetime'
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if ($model->expires_at) {
                return;
            }
            $plan = Plan::with('package')->find($model->plan_id);
            if ($plan && $plan->package && $plan->package->validity) {
                $model->expires_at = now()->addDays((int)$plan->package->validity);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function plans()
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    public function receipt()
    {
        return $this->belongsTo(Receipt::class);
    }

    public function scopeValidHappimyndApp($query)
    {
        return $query->Valid()->HappimyndApp();
    }

    public function scopeValid($query)
    {
        return $query->where('valid', true)->NotExpired();
    }

    public function scopeNotExpired($query)
    {
        return $query->where(function ($query) {
            $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
        });
    }

    public function isExpired()
    {
        return $this->expires_at !== null && $this->expires_at <= now();
    }
    public function scopeHappimyndApp($query)
    {
        return $query->where('plan_id', 4)->orWhere('plan_id', 5);
    }

    public function scopeActivePlan($query)
    {
        return $query->where('percentage_covered', "!=", "100.00");
    }

    public function scopeCompletedPlan($query)
    {
        return $query->where('percentage_covered', "=", "100.00");
    }

    public function tokenPlan()
    {
        return $this->hasOne(TokenPlan::class);
    }

    public function isCompleted()
    {
        return $this->percentage_covered == 100.00;
    }
}
