<?php

namespace App\Models\ChatBot;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChatBotQuestion extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'chat_bot_questions';

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
    protected $fillable = ['chat_bot_category_id', 'language', 'question'];

    /**
     * Category.
     */
    public function category()
    {
        return $this->belongsTo(ChatBotCategory::class, 'chat_bot_category_id');
    }

    /**
     * Options.
     */
    public function options()
    {
        return $this->hasMany(ChatBotOption::class, 'chat_bot_question_id');
    }
}
