<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model
{
    use HasFactory;

    protected $table = 'product_attributes';

    protected $fillable = [
        'name',
        'code',
        'input_type',
        'is_variation',
        'is_active',
    ];

    protected $casts = [
        'is_variation' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected $appends = ['status'];

    public function getStatusAttribute(): string
    {
        return $this->is_active ? 'ACTIVE' : 'INACTIVE';
    }

    public function values()
    {
        return $this->hasMany(ProductAttributeValue::class, 'product_attribute_id')
            ->orderBy('sort_order');
    }
}
