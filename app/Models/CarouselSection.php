<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarouselSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'data_group_id',
    ];
    public function dataContents() {
        return $this->hasMany(DataContent::class)->orderBy('preference');
    }
}
