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
        'use_count'
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
        return ($this->isActive() && !$this->isExpired());
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
        if($this->use_limit == $this->use_count){
            return true;
        }else{
            return false;
        }
    }
}
