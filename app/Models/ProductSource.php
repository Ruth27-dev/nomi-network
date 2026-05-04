<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSource extends Model
{
    protected $table = 'product_sources';

    protected $fillable = [
        'name_en',
        'name_kh',
        'contact_info',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
