<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Page extends Model
{
    use HasFactory;
    protected $table = 'pages';
    protected $fillable = [
        'page',
        'title',
        'short_detail',
        'content',
        'ordering',
        'image',
        'status',
        'user_id',
    ];

    protected $casts = [
        'title'         => 'array',
        'short_detail'   => 'array',
        'content'       => 'array',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    protected $appends = ['image_url'];
    protected $translatable = ['title', 'short_detail', 'content'];


    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/item/' . $this->image) : asset('images/logo.jpg');
    }
    public function getTranslatable()
    {
        return $this->translatable ?? [];
    }
}
