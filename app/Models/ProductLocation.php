<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductLocation extends Model
{
    protected $table = 'product_locations';

    protected $fillable = [
        'name_en',
        'location_type',
        'address',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $appends = ['status'];

    public function getStatusAttribute(): string
    {
        return $this->is_active ? 'ACTIVE' : 'INACTIVE';
    }
}
