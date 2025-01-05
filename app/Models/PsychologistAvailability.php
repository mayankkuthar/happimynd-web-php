<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PsychologistAvailability extends Pivot
{
    use HasFactory;
    protected $table = 'psychologist_availabilities';
    protected $guarded = [''];

    public function psychologist()
    {
        return $this->belongsTo(Psychologist::class);
    }

    public function availability()
    {
        return $this->belongsTo(Availability::class);
    }
}
