<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DynamicBundlePlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'package_id',
        'plan_id',
        'sessions',
    ];

}
