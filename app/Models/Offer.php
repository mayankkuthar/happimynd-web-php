<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Offer extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'offers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'discount',
        'price',
        'special_inaugral_price',
        'valid',
        'start',
        'end',
        'plan_id'
    ];

    public function package()
    {
        return $this->belongsTo(Plan::class, 'plan_id', 'id');
    }
}
