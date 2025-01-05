<?php

namespace App\Models\ChatBot;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChatBotAssessment extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'chat_bot_assessments';

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = ['deleted_at', 'updated_at', 'created_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'chat_bot_category_id', 'score'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['report'];

    /**
     * User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Category.
     */
    public function category()
    {
        return $this->belongsTo(ChatBotCategory::class, 'chat_bot_category_id', 'id');
    }

    /**
     * Interpretation.
     */
    public function getReportAttribute()
    {
        // Original score
        $score = $calculatedScore = $this->score;

        // Calculation step
        $calculationStep = $this->category->calculation_step_macro;

        // Multiply
        if (str_contains($calculationStep, '*')) {
            $parts = explode('*', $calculationStep);
            $amount = end($parts);
            $calculatedScore = $score * $amount;
        }

        // Divide
        if (str_contains($calculationStep, '/')) {
            $parts = explode('/', $calculationStep);
            $amount = end($parts);
            $calculatedScore = $score / $amount;
        }

        $reportCharacteristic = $this->category->reportCharacteristics()
            ->where('minimum', '<=', $calculatedScore)
            ->where('maximum', '>=', $calculatedScore)
            ->first();

        if ($reportCharacteristic) {
            return $reportCharacteristic;
        }

        return null;
    }
}
