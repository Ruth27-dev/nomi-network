<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    protected $table = 'categories';
    protected $fillable = [
        'title',
        'description',
        'sequence',
        'status',
        'image',
        'slug',
        'user_id',
    ];

    protected $casts = [
        'title'         => 'array',
        'description'   => 'array',
    ];
    protected array $translatable = ['title', 'description'];
    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/category/' . $this->image) : asset("images/no.png");
    }
}
