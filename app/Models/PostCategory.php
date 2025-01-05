<?php

namespace App\Models;

use App\Models\Post;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PostCategory extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'file_type' => 'array'
    ];

    public function post(){

        return $this->hasMany(Post::class);
    }
}
