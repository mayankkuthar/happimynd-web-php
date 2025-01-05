<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HappiselfQuestionOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'happiself_content_id',
        'question_type',
        'option',
    ];


    public function getOptionAttribute($value){
        if($this->question_type == 'match'){
            return json_decode($value);
        }else{
            return $value;
        }
    }
}
