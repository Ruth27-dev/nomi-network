<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';

    protected $fillable = [
        'order_no',
        'user_id',
        'user_address_id',
        'shipping_method_id',
        'shipping_method_title',
        'recipient_name',
        'recipient_phone',
        'shipping_address',
        'note',
        'sub_total',
        'shipping_fee',
        'discount_amount',
        'grand_total',
        'payment_method',
        'payment_status',
        'status',
        'completed_at',
    ];

    protected $casts = [
        'sub_total' => 'float',
        'shipping_fee' => 'float',
        'discount_amount' => 'float',
        'grand_total' => 'float',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function address()
    {
        return $this->belongsTo(UserAddress::class, 'user_address_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }
}

