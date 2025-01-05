<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VerifyUser extends Model
{
    use HasFactory, SoftDeletes;


    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'verify_users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'mobile_otp',
        'email_otp',
        'mobile_verify',
        'email_verify',
        'user_id',
        'forget_email_permission',
        'forget_mobile_permission',
        'subscribe_newsletter_blog',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isEmailVerified()
    {
        // dd($this->email_verify);
        return $this->email_verify == 1;
    }

    public function isMobileVerified()
    {
        return $this->mobile_verify == 1;
    }
}
