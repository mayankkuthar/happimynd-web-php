<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class VerifyPsychologist extends Model
{
    use HasFactory,SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'verify_psychologists';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email_otp',
        'email_verify',
        'psychologist_id',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime'
    ];

    public function psychologist()
    {
        return $this->belongsTo(Psychologist::class);
    }

    public function isEmailVerified()
    {
        // dd($this->email_verify);
        return $this->email_verify == 1;
    }

}
