<?php

namespace App\Models\ChatBot;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatBotReportCharacteristic extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'chat_bot_report_characteristics';

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = ['deleted_at', 'updated_at', 'created_at'];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'chat_bot_category_id',
        'minimum',
        'maximum',
        'interpretation',
    ];

    /**
     * Category.
     */
    public function category()
    {
        return $this->belongsTo(ChatBotCategory::class);
    }
}
