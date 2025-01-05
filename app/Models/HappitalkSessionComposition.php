<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HappitalkSessionComposition extends Model
{
    use HasFactory;

    protected $fillable = [
        'happitalk_session_id',
        'twillio_composition_id'
    ];
    
}
