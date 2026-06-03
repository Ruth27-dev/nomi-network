<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaywayTransaction extends Model
{
    use HasFactory;
    protected $table = 'payway_transactions';
    protected $fillable =
    [
        "order_id",
        "donation_id",
        "tran_id",
        "is_update",
        "tran_type",
        "order_type"
    ];
}
