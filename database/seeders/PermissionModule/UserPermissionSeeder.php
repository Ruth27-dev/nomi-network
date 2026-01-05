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
                'display_name'  => json_encode([
                    'en' => 'View Data Listing',
                    'km' => 'មើលបញ្ជីទិន្នន័យ',
                ]),
                'name'          => 'user-admin-view',
                'guard_name'    => 'admin',
                'module_id'     => $userAdmin->id,
            ],
            [
                'display_name'  => json_encode([
                    'en' => 'Create Data',
                    'km' => 'បង្កើតទិន្នន័យ',
                ]),
                'name'          => 'user-admin-create',
                'guard_name'    => 'admin',
                'module_id'     => $userAdmin->id,
            ],
            [
                'display_name'  => json_encode([
                    'en' => 'Edit Data',
                    'km' => 'កែប្រែទិន្នន័យ',
                ]),
                'name'          => 'user-admin-update',
                'guard_name'    => 'admin',
                'module_id'     => $userAdmin->id,
            ],
            [
                'display_name'  => json_encode([
                    'en' => 'Move data to Trash',
                    'km' => 'ផ្ទេរទិន្នន័យទៅធុងសំរាម',
                ]),
                'name'          => 'user-delete',
                'guard_name'    => 'admin',
                'module_id'     => $userAdmin->id,
            ],
            [
                'display_name'  => json_encode([
                    'en' => 'Restore Data',
                    'km' => 'ស្ដារទិន្នន័យ',
                ]),
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
                'display_name'  => json_encode([
                    'en' => 'View Data Listing',
                    'km' => 'មើលបញ្ជីទិន្នន័យ',
                ]),
                'name'          => 'role-view',
                'guard_name'    => 'admin',
                'module_id'     => $role->id,
            ],
            [
                'display_name'  => json_encode([
                    'en' => 'Create Data',
                    'km' => 'បង្កើតទិន្នន័យ',
                ]),
                'name'          => 'role-create',
                'guard_name'    => 'admin',
                'module_id'     => $role->id,
            ],
            [
                'display_name'  => json_encode([
                    'en' => 'Edit Data',
                    'km' => 'កែប្រែទិន្នន័យ',
                ]),
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
