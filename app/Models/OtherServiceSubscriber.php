<?php

namespace App\Models;

use App\Models\OtherService;
use App\Models\ServicesReceipt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OtherServiceSubscriber extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'paid' => 'boolean'
    ];

    public function otherService()
    {
        return $this->belongsTo(OtherService::class, 'other_service_id');
    }
    public function receipt()
    {
        return $this->hasOne(ServicesReceipt::class);
    }
}