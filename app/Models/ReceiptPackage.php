<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReceiptPackage extends Model
{
    use HasFactory, SoftDeletes;


    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'receipt_packages';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'receipt_id',
        'plan_id',
        'amount'
    ];

    public function receipt()
    {
        return $this->belongsTo(Receipt::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }
}
