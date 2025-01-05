<?php

namespace App\Models;

use App\Models\EducationServiceAuthor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceMetaData extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function author()
    {
        return $this->belongsTo(EducationServiceAuthor::class, 'education_service_author_id');
    }

    public function otherService()
    {
        return $this->belongsTo(OtherService::class);
    }
}