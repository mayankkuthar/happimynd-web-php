<?php

namespace App\Models;

use App\Models\ServiceType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceTypeGroup extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function type()
    {
        return $this->belongsTo(ServiceType::class, 'service_type_id');
    }

    public function group()
    {
        return $this->hasMany(ServiceTypeGroup::class);
    }

    public function service()
    {
        return $this->hasMany(OtherService::class, 'service_type_group_id');
    }
}