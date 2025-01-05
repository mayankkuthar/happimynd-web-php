<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Specialization extends Model
{
    use HasFactory;

    public function psychologist()
    {
        return $this->belongsToMany(Psychologist::class)
            ->using(PsychologistSpecialization::class);
    }
}
