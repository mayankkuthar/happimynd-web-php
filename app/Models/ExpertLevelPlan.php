<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ExpertLevelPlan extends Pivot
{
    use HasFactory;

    protected $guard = [''];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function expertLevel()
    {
        return $this->belongsTo(ExpertLevel::class);
    }
}
