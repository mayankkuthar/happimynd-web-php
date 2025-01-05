<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ThriveCode extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'thrive_codes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'code',
        'expired_at',
        'organization_id',
        'status',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'expired_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
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
     * available thrive code
     *
     * @param Builder $query
     * @return void
     */
    public function scopeAvaliableCode($query)
    {
        return $query->whereNull('user_id')->ValidTokens();
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

    public function scopeThriveUsed($query)
    {
        return $query->orderBy('expired_at', 'desc');
    }
}
