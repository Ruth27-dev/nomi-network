<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Permission extends Model
{
    use HasFactory;
    protected $table = 'permissions';
    protected $fillable = [
        'module_id',
        'name',
        'display_name',
        'guard_name',
    ];

    protected $hidden = ['pivot'];
    
    public function ModelHasPermission()
    {
        return $this->hasMany(ModelHasPermission::class, 'permission_id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_has_permissions', 'permission_id', 'role_id');
    }
}
