<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    protected $table = 'donations';

    protected $fillable = [
        'tran_id',
        'user_id',
        'donation_type',
        'amount',
        'firstname',
        'lastname',
        'email',
        'payment_option',
        'payment_status',
        'note',
    ];

    protected $casts = [
        'amount' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
