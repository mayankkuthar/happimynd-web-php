<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HappibuddyMonthlyReport extends Model
{
    use HasFactory;


    protected $table = "happibuddy_monthy_reports";

    protected $fillable = [
        'user_id',
        'psychologist_id',
        'session_status',
        'presenting_complaints',
        'session_summary',
        'hardword_asigned',
        'plan_for_next_session',
    ];

}
