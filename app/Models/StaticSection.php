<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaticSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'data_group_id',
        'section',
        'data_content_id',
    ];

    public function dataGroup() {
        return $this->belongsTo(DataGroup::class);
    }

    public function dataContent() {
        return $this->belongsTo(DataContent::class);
    }


}
