<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EditButton extends Model
{
    use HasFactory;
    protected $fillable = [
        'id','button_content','page_name'
    ];
}
