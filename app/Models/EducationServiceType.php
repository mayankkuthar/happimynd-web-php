<?php

namespace App\Models;

use App\Models\EducationalService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EducationServiceType extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function service()
    {
        return $this->hasMany(EducationalService::class, 'service_type_id');
    }
}