<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HappiguideSessionComposition extends Model
{
    use HasFactory;

    protected $fillable = [
        'happiguide_session_id',
        'twillio_composition_id'
    ];

}
