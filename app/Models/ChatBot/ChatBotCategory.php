<?php

namespace App\Models\ChatBot;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChatBotCategory extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'chat_bot_categories';

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
    protected $fillable = [
        'name',
        'calculation_step_macro'
    ];

    /**
     * Questions.
     */
    public function questions()
    {
        return $this->hasMany(ChatBotQuestion::class, 'chat_bot_category_id', 'id');
    }

    /**
     * Options.
     */
    public function options()
    {
        return $this->hasManyThrough(ChatBotOption::class, ChatBotQuestion::class, 'chat_bot_category_id', 'chat_bot_question_id', 'id', 'id');
    }

    /**
     * Report characteristics.
     */
    public function reportCharacteristics()
    {
        return $this->hasMany(ChatBotReportCharacteristic::class, 'chat_bot_category_id');
    }
}
