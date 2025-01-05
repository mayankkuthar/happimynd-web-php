<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HappiguideSessionOpinionPsychologist extends Model
{
    use HasFactory;

    protected $fillable = [
        'psychologist_id',
        'happiguide_session_id',
        'session_status',
        'presenting_complaints',
        'session_summary',
        'hardword_asigned',
        'plan_for_next_session',
    ];

}
