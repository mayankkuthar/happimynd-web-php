<?php

namespace App\Models;

use App\Models\OtherService;
use App\Models\ServiceTypeGroup;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceType extends Model
{
    use HasFactory;

    public function group()
    {
        return $this->hasMany(ServiceTypeGroup::class);
    }

    public function services()
    {
        return $this->hasManyThrough(OtherService::class, ServiceTypeGroup::class);
    }
}