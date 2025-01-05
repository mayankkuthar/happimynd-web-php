<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TokenPlan extends Model
{
    use HasFactory;
    protected $fillable = [
        'token_id',
        'plan_id',
        'status'
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function bundleStatus()
    {
        return $this->belongsTo(BundleStatus::class);
    }
}
