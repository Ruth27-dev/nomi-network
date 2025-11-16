<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ListOfValue extends Model
{
    use SoftDeletes;

    protected $table = 'list_of_values';
    protected $fillable = [
        'branch_id',
        'code',
        'type',
        'title',
        'description',
        'status',
        'add_on',
        'sequence',
        'image',
        'user_id',
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/list-of-value/' . $this->image);
        }

        return asset('images/no.png');
    }




    protected $casts = [
        'title'         => 'array',
        'description'   => 'array',
        'add_on'        => 'array',
    ];
    protected array $translatable = ['title', 'description', 'add_on'];

    public function getTranslatable()
    {
        return $this->translatable ?? [];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_chef_groups', 'chef_group_id', 'user_id');
    }
}
