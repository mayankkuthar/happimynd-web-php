<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Receipt extends Model
{
    use HasFactory, SoftDeletes;


    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'receipts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'marchant_name',
        'amount',
        'currency',
        'status',
        'order_id',
        'payment_id',
        'user_id',
        'plan_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plans()
    {
        return $this->hasMany(ReceiptPackage::class);
    }

    public function couponReceipt()
    {
        return $this->hasOne(CouponReceipt::class);
    }

    public function isPaymentCompleted()
    {
        return $this->status == 1;
    }
}
