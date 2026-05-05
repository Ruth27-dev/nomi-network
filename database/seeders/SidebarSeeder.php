<?php

namespace Database\Seeders;

use App\Models\Menu;
use Database\Seeders\MenuModule\OrderSeeder;
use Database\Seeders\MenuModule\PageSeeder;
use Database\Seeders\MenuModule\ProductSeeder;
use Database\Seeders\MenuModule\SettingSeeder;
use Database\Seeders\MenuModule\UserSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class SidebarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
             Schema::disableForeignKeyConstraints();
        Menu::truncate();
        Schema::enableForeignKeyConstraints();
        $this->call(ProductSeeder::class);
        $this->call(OrderSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(SettingSeeder::class);
        $this->call(PageSeeder::class);
    }
}
