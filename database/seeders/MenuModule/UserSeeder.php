<?php

namespace Database\Seeders\MenuModule;

use App\Models\Menu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //User
        $user = Menu::create([
            'name' => json_encode(['en' => 'User Management', 'km' => 'ការគ្រប់គ្រងអ្នកប្រើប្រាស់']),
            'icon'  => 'persons',
            'active' => 'admin/user/*',
            'ordering' => 2,
            'permission' => array('user-view', 'role-view'),
        ]);

        Menu::create([
            'parent_id' => $user->id,
            'name'      => json_encode([
                'en'    => "Customer",
                'km'    => "អតិថិជន",
            ]),
            'path'          => 'admin/user/user/customer/list',
            'active'        => 'admin/user/user/customer/list*',
            'ordering'      => 2,
            'permission'    => array('user-view'),
        ]);

        Menu::create([
            'parent_id' => $user->id,
            'name'      => json_encode([
                'en'    => "Operation User",
                'km'    => "អ្នកប្រើប្រាស់ប្រតិបត្តិការ",
            ]),
            'path'          => 'admin/user/user/operation/list',
            'active'        => 'admin/user/user/operation/list*',
            'ordering'      => 3,
            'permission'    => array('user-view'),
        ]);

        //Role
        Menu::create([
            'parent_id' => $user->id,
            'name' => json_encode(['en' => 'User Role', 'km' => 'តួនាទីអ្នកប្រើប្រាស់']),
            'path' => 'admin/user/user-role/list',
            'active' => 'admin/user/user-role/*',
            'ordering' => 5,
            'permission' => array('role-view'),
        ]);
    }
}
