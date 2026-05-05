<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductVariation extends Model
{
    use HasFactory;
    protected $table = 'product_variations';
    protected $fillable = [
        'product_id',
        'sku',
        'barcode',
        'name',
        'combination_key',
        'price',
        'stock',
        'image_url',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
    protected $appends = ['title', 'status', 'description'];

    public function getTitleAttribute(): array
    {
        return ['en' => $this->name, 'km' => null];
    }

    public function getStatusAttribute(): string
    {
        return $this->is_active ? 'ACTIVE' : 'INACTIVE';
    }

    public function getDescriptionAttribute(): array
    {
        return ['en' => null, 'km' => null];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function images()
    {
        return $this->hasMany(Gallery::class, 'foreign_id')
            ->where('foreign_model', self::class);
    }

    public function discounts()
    {
        return $this->belongsToMany(Discount::class, 'product_variation_discount', 'product_variation_id', 'discount_id');
    }
}
