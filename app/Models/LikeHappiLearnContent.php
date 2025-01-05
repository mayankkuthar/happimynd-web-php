<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LikeHappiLearnContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'happi_learn_content_id',
    ];

}
