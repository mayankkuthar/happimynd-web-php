<?php

namespace App\Models\ChatBot;

use App\Models\UserProfile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recommendation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'user_profile_id',
        'recommendation_category_id',
        'title_1',
        'url_1',
        'title_2',
        'url_2',
        'title_3',
        'url_3',
    ];

    /**
     * Category.
     */
    public function userProfile()
    {
        return $this->belongsTo(UserProfile::class);
    }

    /**
     * Category.
     */
    public function recommendationCategory()
    {
        return $this->belongsTo(RecommendationCategory::class);
    }
}
