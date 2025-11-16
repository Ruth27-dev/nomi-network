<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;
    protected $table = 'roles';
    protected $fillable = [
        'name',
        'display_name',
        'guard_name',
        'status',
    ];

    protected $appends = ['can_action'];
    public function getCanActionAttribute()
    {
        if ($this->name == 'chef' || $this->name == 'operator' || $this->name == 'admin' || $this->name == 'pos') {
            return false;
        } else {
            return true;
        }
    }

    protected $casts = [
        'display_name'         => 'array',
    ];
    protected array $translatable = ['display_name'];

    public function getTranslatable()
    {
        return $this->translatable ?? [];
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_has_permissions', 'role_id', 'permission_id')->select(['permissions.id', 'permissions.name', 'permissions.guard_name', 'permissions.display_name']);
    }
}
