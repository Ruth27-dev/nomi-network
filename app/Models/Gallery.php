<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    protected $table = 'galleries';
    protected $fillable = [
        'foreign_id',
        'foreign_model',
        'image',
        'title',
        'description',
        'user_id',
    ];
    protected $appends = ['url'];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function foreign()
    {
        return $this->morphTo(__FUNCTION__, 'foreign_model', 'foreign_id');
    }

    public function getUrlAttribute()
    {
        if (!$this->image) return null;
        // New records store a relative path (e.g. "product/images/file.jpg")
        if (str_contains($this->image, '/')) {
            return asset('storage/' . $this->image);
        }
        // Legacy variation images stored without a path prefix
        return asset('storage/product/variation/' . $this->image);
    }
}
