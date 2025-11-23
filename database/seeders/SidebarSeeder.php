<?php

namespace Database\Seeders;

use App\Models\Menu;
use Database\Seeders\MenuModule\HomePageConfigSeeder;
use Database\Seeders\MenuModule\HomePageSeeder;
use Database\Seeders\MenuModule\ItemSeeder;
use Database\Seeders\MenuModule\PageSeeder;
use Database\Seeders\MenuModule\ProductSeeder;
use Database\Seeders\MenuModule\ReportSeeder;
use Database\Seeders\MenuModule\SettingSeeder;
use Database\Seeders\MenuModule\UserSeeder;
use Database\Seeders\MenuModule\StockInventorySeeder;
use Illuminate\Database\Seeder;

class SidebarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Menu::truncate();
        $this->call(ProductSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(SettingSeeder::class);
    }
}
