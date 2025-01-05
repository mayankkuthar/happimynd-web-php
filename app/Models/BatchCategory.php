<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class BatchCategory extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'batch_category';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'batch_id',
        'category_id',
        'calculation_step_macro',
        'sort_order'
    ];

    public function questions()
    {
        // $user_language = Auth::user()->language;
        // return $this->hasMany(Question::class)->where('language' , $user_language);
        return $this->hasMany(Question::class);

    }

    public function questions_english()
    { 
        return $this->hasMany(Question::class)->where('language' , 'english');

    }



    public function questions_app()
    {
        $user_language = Auth::user()->language;
        return $this->hasMany(Question::class  , 'batch_category_id')->where('language' , $user_language);
    }


    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function reportCharacteristic()
    {
        return $this->hasMany(ReportCharacteristic::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }
}
