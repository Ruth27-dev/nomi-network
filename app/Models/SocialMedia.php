<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SocialMedia extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'social_media';
    protected $fillable = [
        'title',
        'ordering',
        'image',
        'url',
        'status',
        'user_id'
    ];

    protected $casts = [
        'title'         => 'array',
    ];
    protected array $translatable = ['title'];

    public function getTranslatable()
    {
        return $this->translatable ?? [];
    }

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        return UploadFile::resolvePublicUrl($this->image, 'social-media', asset('images/no.jpg'));
    }
}
