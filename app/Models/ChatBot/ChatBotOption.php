<?php

namespace App\Models\ChatBot;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatBotOption extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'chat_bot_options';

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
        'chat_bot_question_id',
        'option',
        'score',
    ];

    /**
     * Question.
     */
    public function question()
    {
        return $this->belongsTo(ChatBotQuestion::class, 'chat_bot_question_id');
    }
}
