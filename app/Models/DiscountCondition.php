<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountCondition extends Model
{
    protected $table = 'discount_conditions';

    protected $fillable = [
        'discount_id',
        'type',
        'amount',
    ];
}
