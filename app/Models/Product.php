<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Gallery;


class Product extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'products';

    protected $fillable = [
        'category_id',
        'sku',
        'name_en',
        'name_kh',
        'description_en',
        'description_kh',
        'price',
        'stock',
        'is_preorder',
        'has_variation',
        'is_active',
        'product_source_id',
        'product_location_id',
        'source_en',
        'source_kh',
    ];

    protected $casts = [
        'is_preorder' => 'boolean',
        'has_variation' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected $appends = ['title', 'description', 'status', 'code'];

    public function categories()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function productVariations()
    {
        return $this->hasMany(ProductVariation::class, 'product_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }

    public function images()
    {
        return $this->hasMany(Gallery::class, 'foreign_id')
            ->where('foreign_model', self::class);
    }

    public function scopeWithRelation($query)
    {
        return $query->with(['category', 'images', 'productVariations.images']);
    }

    public function getTitleAttribute(): array
    {
        return [
            'en' => $this->name_en,
            'km' => $this->name_kh,
        ];
    }

    public function getDescriptionAttribute(): array
    {
        return [
            'en' => $this->description_en,
            'km' => $this->description_kh,
        ];
    }

    public function getStatusAttribute(): string
    {
        return $this->is_active ? 'ACTIVE' : 'INACTIVE';
    }

    public function getCodeAttribute(): ?string
    {
        return $this->sku;
    }
}
