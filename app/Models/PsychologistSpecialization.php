<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PsychologistSpecialization extends Pivot
{
    use HasFactory;

    protected $table = 'psychologist_specializations';

    public function specialization()
    {
        return $this->belongsTo(Specialization::class);
    }

    public function psychlogist()
    {
        return $this->belongsTo(Psychologist::class);
    }
}
