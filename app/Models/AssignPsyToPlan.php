<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignPsyToPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan_name',
        'psychologist_id',
        'last_psy_assign_for_guide',
        'last_psy_assign_for_buddy',
    ];
    
}
