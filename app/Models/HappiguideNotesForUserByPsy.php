<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\HappiguideSession;


class HappiguideNotesForUserByPsy extends Model
{
    use HasFactory;


    protected $table = 'happiguide_notes_for_user_by_psys';

    protected $fillable = [
        'guide_session_id',
        'case_history',
        'username',
        'time',
        'duration',
        'name_of_therapist',
        'age',
        'gender',
        'occupation',
        'qualification',
        'presenting_complaints',
        'past_psychology_history',
        'medical_history',
        'family_psychological_histroy',
        'session_summary',
        'diagnosis',
        'plan_for_therpy_treatment',
    ];

    public function guideSessionDetail(){
        return $this->belongsTo(HappiguideSession::class , 'guide_session_id')->with('userDetail','psychologistDetail');
    }


}
