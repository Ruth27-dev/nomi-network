<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCartItem extends Model
{
    protected $table = 'user_cart_items';

    protected $fillable = [
        'user_id',
        'product_id',
        'product_variation_id',
        'quantity',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function variation()
    {
        return $this->belongsTo(ProductVariation::class, 'product_variation_id');
    }
}
