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
        'title',
        'status',
        'price',
        'size',
        'description',
        'note',
        'is_available',
        'image',
        'user_id',
    ];

    protected $casts = [
        'title'         => 'array',
        'description'   => 'array',
        'note'   => 'array',
    ];

    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/uploads/item/' . $this->image) : asset('images/logo.jpg');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
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
}
