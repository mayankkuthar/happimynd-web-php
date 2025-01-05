<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HappiselfContentAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'happiself_content_id',
        'question_type',
        'answer',
    ];

}