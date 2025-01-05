<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'questions';

    /**
     * The number of models to return for pagination.
     *
     * @var int
     */
    protected $perPage = 36;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'language',
        'question',
        'category_id',
        'batch_category_id',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function option()
    {
        return $this->belongsToMany(Option::class, 'option_questions')->withPivot('id', 'weightage');
    }

    public function batch()
    {
        return $this->belongsToThrough(BatchCategory::class, Batch::class);
    }

    public function batchCategory()
    {
        return $this->belongsTo(BatchCategory::class);
    }

    public function optionQuestion()
    {
        return $this->hasMany(OptionQuestion::class, 'question_id');
    }
}
