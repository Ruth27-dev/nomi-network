<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;


class Product extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'products';

    protected $fillable = [
        'branch_id',
        'code',
        'title',
        'unit_id',
        'is_sellable',
        'is_consumable',
        'is_vat',
        'is_popular',
        'status',
        'type',
        'description',
        'image',
        'user_id',
    ];

    protected $casts = [
        'title'         => 'array',
        'description'   => 'array',
        'is_sellable'   => 'boolean',
        'is_consumable' => 'boolean',
        'is_vat'        => 'boolean',
        'is_popular'    => 'boolean',
    ];

    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/product/' . $this->image) : asset("images/no.jpg");
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_categories', 'product_id', 'category_id');
    }

    public function galleries()
    {
        return $this->morphMany(Gallery::class, 'foreign', 'foreign_model', 'foreign_id');
    }

    public function productVariation()
    {
        return $this->hasMany(ProductVariation::class, 'product_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeWithRelation($query)
    {
        return $query->with(['categories', 'productVariation','galleries']);
    }
}
