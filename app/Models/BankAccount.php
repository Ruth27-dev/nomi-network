<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BankAccount extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'bank_accounts';
    protected $fillable = [
        'branch_id',
        'bank_name',
        'bank_number',
        'account_name',
        'status',
        'ordering',
        'qr_code',
        'status',
        'user_id',
    ];

    protected $appends = ['qr_code_url'];

    public function getQrCodeUrlAttribute()
    {
        return $this->qr_code  ? asset('storage/uploads/bank-account/' . $this->qr_code) : asset("images/logo.jpg");
    }
}
