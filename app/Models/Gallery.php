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
        return asset('storage/product/variation/' . $this->image);
    }
}
