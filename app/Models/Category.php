<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'categories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'acronymn',
        'name_in_report',
        'color'
    ];

    public function post()
    {
        return $this->belongsToMany(Post::class);
    }

    public function question()
    {
        return $this->hasMany(Question::class);
    }

    public function batch()
    {
        return $this->belongsToMany(Batch::class);
    }

    public function batchCategory()
    {
        return $this->hasOne(BatchCategory::class);
    }

    public function reportCharacteristics()
    {
        return $this->hasMany(ReportCharacteristic::class);
    }
}
