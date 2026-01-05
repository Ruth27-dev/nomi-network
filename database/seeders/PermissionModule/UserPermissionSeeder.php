<?php

namespace Database\Seeders\PermissionModule;

use App\Models\ModulePermission;
use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // User
        $user = ModulePermission::create([
            'display_name'  => json_encode([
                'en' => 'User Management',
                'km' => 'ការគ្រប់គ្រងអ្នកប្រើប្រាស់',
            ]),
            'sort_no'       => 2,
        ]);

        $userAdmin = ModulePermission::create([
            'parent_id'     =>  $user->id,
            'display_name'  => json_encode([
                'en' => 'User ',
                'km' => 'អ្នកប្រើប្រាស់',
            ]),
            'sort_no'       => $this->increaseIndex(),
        ]);

        Permission::insert([
            [
                'display_name'  => json_encode(config('permission_module.action.view')),
                'name'          => 'user-admin-view',
                'guard_name'    => 'admin',
                'module_id'     => $userAdmin->id,
            ],
            [
                'display_name'  => json_encode(config('permission_module.action.create')),
                'name'          => 'user-admin-create',
                'guard_name'    => 'admin',
                'module_id'     => $userAdmin->id,
            ],
            [
                'display_name'  => json_encode(config('permission_module.action.update')),
                'name'          => 'user-admin-update',
                'guard_name'    => 'admin',
                'module_id'     => $userAdmin->id,
            ],
            [
                'display_name'  => json_encode(config('permission_module.action.delete')),
                'name'          => 'user-delete',
                'guard_name'    => 'admin',
                'module_id'     => $userAdmin->id,
            ],
            [
                'display_name'  => json_encode(config('permission_module.action.restore')),
                'name'          => 'user-restore',
                'guard_name'    => 'admin',
                'module_id'     => $userAdmin->id,
            ],
        ]);

        $role = ModulePermission::create([
            'parent_id'     =>  $user->id,
            'display_name'  => json_encode([
                'en' => 'Role',
                'km' => 'តួនាទី',
            ]),
            'sort_no'       => $this->increaseIndex(),
        ]);

        Permission::insert([
            [
                'display_name'  => json_encode(config('permission_module.action.view')),
                'name'          => 'role-view',
                'guard_name'    => 'admin',
                'module_id'     => $role->id,
            ],
            [
                'display_name'  => json_encode(config('permission_module.action.create')),
                'name'          => 'role-create',
                'guard_name'    => 'admin',
                'module_id'     => $role->id,
            ],
            [
                'display_name'  => json_encode(config('permission_module.action.update')),
                'name'          => 'role-update',
                'guard_name'    => 'admin',
                'module_id'     => $role->id,
            ],
        ]);
    }

    public $index = 0;
    public function increaseIndex()
    {
        return $this->index += 1;
    }
}
