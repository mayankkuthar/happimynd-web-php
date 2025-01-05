<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Psychologist;
use App\Models\Organization;

class AssignPsyToOrgForTalk extends Model
{
    use HasFactory;


    protected $fillable = [
        'organization_id',
        'psychologist_id',
    ];
    

    public function psychologist(){
        return $this->belongsTo(Psychologist::class , 'psychologist_id');
    }

    public function organization(){
        return $this->belongsTo(Organization::class , 'organization_id');
    }

}
