<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Discount extends Model
{
    use SoftDeletes;

    protected $table = 'discounts';

    protected $fillable = [
        'type',
        'title',
        'code',
        'discount_amount',
        'discount_type',
        'start_date',
        'end_date',
        'usage_limit',
        'usage_per_customer',
        'is_flat_discount',
        'remark',
        'status',
        'image',
        'user_id',
    ];

    protected $casts = [
        'title'  => 'array',
        'remark' => 'array',
    ];

    public function products()
    {
        return $this->belongsToMany(ProductVariation::class, 'product_variation_discount', 'discount_id', 'product_variation_id');
    }

    public function conditions()
    {
        return $this->hasMany(DiscountCondition::class, 'discount_id');
    }

    public function usage()
    {
        return $this->hasMany(OrderDiscountUsage::class, 'discount_id');
    }
}
