<?php

namespace Database\Seeders;

use App\Models\ModulePermission;
use App\Models\Permission;
use Database\Seeders\MenuModule\ProductSeeder;
use Database\Seeders\PermissionModule\SettingSeeder;
use Database\Seeders\PermissionModule\UserPermissionSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Schema::disableForeignKeyConstraints();
        ModulePermission::truncate();
        Permission::truncate();
        Schema::enableForeignKeyConstraints();
        $this->call(UserPermissionSeeder::class);
        $this->call(ProductSeeder::class);
        $this->call(SettingSeeder::class);
    }
}
