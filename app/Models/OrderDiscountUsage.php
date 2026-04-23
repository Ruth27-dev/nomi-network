<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDiscountUsage extends Model
{
    protected $table = 'order_discount_usages';

    protected $fillable = [
        'discount_id',
        'customer_id',
        'order_id',
        'order_detail_id',
        'discount_data',
        'user_id',
    ];

    protected $casts = [
        'discount_data' => 'array',
    ];

    public function discount()
    {
        return $this->belongsTo(Discount::class, 'discount_id');
    }
}
