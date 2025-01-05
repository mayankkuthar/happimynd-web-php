<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HappitalkTax extends Model
{
    use HasFactory;

    protected $fillable = [
        'tds_percentage',
    ];
    
}
