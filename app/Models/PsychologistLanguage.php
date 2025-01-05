<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PsychologistLanguage extends Pivot
{
    use HasFactory;
    public function psychologist()
    {
        return $this->belongsTo(Psychologist::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
