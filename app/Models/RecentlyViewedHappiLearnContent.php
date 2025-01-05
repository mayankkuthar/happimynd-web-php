<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecentlyViewedHappiLearnContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'happi_learn_content_id'
    ];

    public function HappiLearnContent(){
        return $this->belongsTo(HappiLearnContent::class)->where('is_deleted' , 0)->withCount('likes');
    }

}
