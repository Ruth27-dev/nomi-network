<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockHistory extends Model
{
    protected $table = 'stock_history';

    protected $fillable = [
        'order_id',
        'product_id',
        'product_variation_id',
        'quantity',
        'transaction_type',
        'stock_before',
        'stock_after',
    ];
}

