<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Role::truncate();
        Schema::enableForeignKeyConstraints();

        // Customer role — no permissions
        Role::create([
            'name'         => 'customer',
            'display_name' => ['en' => 'Customer', 'km' => 'អតិថិជន'],
            'guard_name'   => 'admin',
            'status'       => 'ACTIVE',
        ]);

        // Admin role — all permissions
        $admin = Role::create([
            'name'         => 'admin',
            'display_name' => ['en' => 'Admin', 'km' => 'អ្នកគ្រប់គ្រង'],
            'guard_name'   => 'admin',
            'status'       => 'ACTIVE',
        ]);

        $allPermissions = Permission::where('guard_name', 'admin')->pluck('id');
        $admin->permissions()->sync($allPermissions);
    }
}
