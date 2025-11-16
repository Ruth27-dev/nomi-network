<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModulePermission extends Model
{
    use HasFactory;
    protected $table = 'module_permissions';

    public function permissions()
    {
        return $this->hasMany(Permission::class, 'module_id');
    }

    public function subModules()
    {
        return $this->hasMany(ModulePermission::class, 'parent_id')->with('subModules.permissions')->orderBy('sort_no');
    }

}
