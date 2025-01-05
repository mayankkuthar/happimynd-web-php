<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReportCharacteristic extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'report_characteristics';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    protected $casts = [
        'included_in_report' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function emoji()
    {
        return $this->belongsTo(RatingPictures::class, 'rating_picture_id');
    }

    public function oldEmoji()
    {
        return $this->hasOne(RatingPictures::class);
    }
    public function getRatingPictureIdAttribute($value)
    {
        return ($value) ? $value : "";
    }
}
