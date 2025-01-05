<?php

namespace App\Models\ChatBot;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecommendationCategory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = ['name'];

    /**
     * Recommendations.
     */
    public function recommendations()
    {
        return $this->hasMany(Recommendation::class);
    }
}
