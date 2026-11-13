<?php

namespace App\Models;

use App\Models\TokenMetaData;
use App\Models\BundleStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Token extends Model
{
    //TODO: in table use bundle_status_id  :ease of tracking
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tokens';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'token',
        'expired_at',
        'organization_id',
        'email',
        'use_limit',
        'use_count',
        'valid_till'
    ];

    protected $casts = [
        'valid_till' => 'date',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function plans()
    {
        return $this->hasMany(TokenPlan::class);
    }

    public function tokenPlans()
    {
        return $this->hasMany(TokenPlan::class);
    }

    public function userToken()
    {
        return $this->hasMany(UserToken::class);
    }

    /**
     * Get all of the category for the Token
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function category()
    {
        return $this->hasMany(CategoryToken::class);
    }

    public function isExpired()
    {
        return $this->expired_at != null;
    }

    public function isActive()
    {
        return $this->status == 'active';
    }

    public function isDisabled()
    {
        return $this->status == 'inactive';
    }

    public function isUsable()
    {
        return ($this->isActive() && !$this->isExpired() && !$this->isValidityOver());
    }

    /**
     * Validity (valid_till) is inclusive: a code valid till 2026-12-31
     * can still be redeemed on 2026-12-31.
     */
    public function isValidityOver()
    {
        return $this->valid_till !== null && $this->valid_till->copy()->endOfDay()->isPast();
    }

    /**
     * Codes redeemed by at least one user
     */
    public function scopeUsedTokens($query)
    {
        return $query->where('use_count', '>', 0);
    }

    /**
     * Codes whose validity date has not passed (or have no validity date)
     */
    public function scopeWithinValidity($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('valid_till')->orWhereDate('valid_till', '>=', now()->toDateString());
        });
    }

    /**
     * Codes whose validity date has passed
     */
    public function scopeValidityOver($query)
    {
        return $query->whereNotNull('valid_till')->whereDate('valid_till', '<', now()->toDateString());
    }

    /**
     * valid tokens
     *
     * @param Builder $query
     * @return void
     */
    public function scopeActiveTokens($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * tokens disabled by admin
     *
     * @param Builder $query
     * @return void
     */
    public function scopeInactiveTokens($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Tokens which are not used and active
     *
     * @param Builder $query
     * @return void
     */
    public function scopeValidTokens($query)
    {
        return $query->whereNull('expired_at')->ActiveTokens();
    }

    /**
     * Tokens which are valid but disabled by admin
     *
     * @param Builder $query
     * @return void
     */
    public function scopeDisabledTokens($query)
    {
        return $query->whereNull('expired_at')->InactiveTokens();
    }

    /**
     * return Used tokens
     *
     * @param Builder $query
     * @return void
     */
    public function scopeExpiredTokens($query)
    {
        return $query->whereNotNull('expired_at');
    }

    /**
     * make token active/usable
     *
     * @param $query
     * @return void
     */
    public function scopeActivateToken($query)
    {
        return $query->update(['status' => 'active']);
    }

    /**
     * make token inactive/not usable
     *
     * @param $query
     * @return void
     */
    public function scopeDeactivateToken($query)
    {
        return $query->update(['status' => 'inactive']);
    }

    /**
     * make disabled tokens usable
     *
     * @param [type] $query
     * @return void
     */
    public function scopeActivateDisabledTokens($query)
    {
        return $query->DisabledTokens()->ActivateToken();
    }

    /**
     * disable active/un-expired tokens
     *
     * @param [type] $query
     * @return void
     */
    public function scopeRevokeValidTokens($query)
    {
        return $query->ValidTokens()->DeactivateToken();
    }

    public function tokenMetaData()
    {
        return $this->belongsTo(TokenMetaData::class);
    }


    public function scopeTokenUsed($query)
    {
        return $query->orderBy('expired_at', 'desc');
    }

    public function isMaxUsed(){
        // >= so a code that somehow went past its limit is still treated as used up
        return $this->use_limit !== null && (int)$this->use_count >= (int)$this->use_limit;
    }
}
