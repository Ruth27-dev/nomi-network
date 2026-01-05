<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Banner extends Model
{
    use SoftDeletes;

    protected $table = 'banners';

    protected $fillable = [
        'title',
        'banner_page',
        'ordering',
        'status',
        'url',
        'image',
        'user_id',
    ];

    protected $casts = [
        'title' => 'array',
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/banner/' . $this->image) : asset('images/no.jpg');
    }
}
