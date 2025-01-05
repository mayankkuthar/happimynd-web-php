<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryToken extends Model
{
    use HasFactory;
    protected $fillable = [
        'token_id',
        'token_category_id'
    ];

    public function category()
    {
        return $this->belongsTo(TokenCategory::class,'token_category_id');
    }
}
