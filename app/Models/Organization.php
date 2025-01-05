<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Storage;

class Organization extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'organizations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    public function token()
    {
        return $this->hasMany(Token::class);
    }

    public function thriveCode()
    {
        return $this->hasMany(ThriveCode::class);
    }

    public function scopeAvaliableOrganization($query)
    {
        return $query->where('active', true)->whereNull('deleted_at');
    }

    public function scopeHappimynd($query)
    {
        return $query->where('name', 'Happimynd')->where('active', true)->whereNull('deleted_at');
    }

    public function scopeRemoveTestOrganization($query)
    {
        return
            $query->where('name', 'not like', '%test%company%')
            ->where('name', 'not like', '%test%company%');
    }

    public function isDeleted()
    {
        return !empty($this->deleted_at);
    }

    public function getUsers()
    {
        return $this->token()->whereHas('userToken')->with('userToken.user')->get()->pluck('userToken.user');
    }


    public function getOrganizationLogoAttribute($value){
        if($value){
            $full_url = Storage::url(config('constants.mediaAssets.organization_logo.folderName') . $value);
            return $full_url;
        }else{
            return null;
        }
    }

}
