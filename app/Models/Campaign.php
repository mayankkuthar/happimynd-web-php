<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'url_parameters',
        'status',
        'plan_id',
        'meta_data'
    ];
    protected $casts = [
        'url_parameters' => 'array',
        'plan_id' => 'array',
        'meta_data' => 'array',
    ];

    public function setPlanIdAttribute($data)
    {
        $this->attributes['plan_id'] = json_encode($data);
    }

    public function isActive()
    {
        return ($this->status == 1);
    }
}
