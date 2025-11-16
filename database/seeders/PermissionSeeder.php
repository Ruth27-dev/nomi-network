<?php

namespace Database\Seeders;

use App\Models\ModulePermission;
use App\Models\Permission;
use Database\Seeders\PermissionModule\DashboardSeeder;
use Database\Seeders\PermissionModule\FeedbackSeeder;
use Database\Seeders\PermissionModule\HomePageSeeder;
use Database\Seeders\PermissionModule\InvoiceSeeder;
use Database\Seeders\PermissionModule\ItemSeeder;
use Database\Seeders\PermissionModule\OrderSeeder;
use Database\Seeders\PermissionModule\PageSeeder;
use Database\Seeders\PermissionModule\PosSeeder;
use Database\Seeders\PermissionModule\ReceiptSeeder;
use Database\Seeders\PermissionModule\ReportSeeder;
use Database\Seeders\PermissionModule\SettingSeeder;
use Database\Seeders\PermissionModule\StockInventorySeeder;
use Database\Seeders\PermissionModule\UserPermissionSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
    }
}
