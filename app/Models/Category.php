<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'categories';
    protected $fillable = [
        'parent_id',
        'title',
        'description',
        'status',
        'slug',
    ];
    protected $casts = ['title' => 'array', 'description' => 'array'];

    protected $appends = ['title', 'status'];

    public function getTitleAttribute(): array
    {
        $title = $this->attributes['title'] ?? null;
        $decoded = is_string($title) ? json_decode($title, true) : $title;
        return is_array($decoded) ? ['en' => $decoded['en'] ?? null, 'km' => $decoded['km'] ?? null] : ['en' => null, 'km' => null];
    }

    public function getStatusAttribute(): string
    {
        return strtoupper((string) ($this->attributes['status'] ?? 'ACTIVE')) === 'ACTIVE' ? 'ACTIVE' : 'INACTIVE';
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'ACTIVE');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }
}
