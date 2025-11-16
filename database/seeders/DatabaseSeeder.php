<?php

namespace Database\Seeders;

use App\Models\ExchangeRate;
use Database\Seeders\Dummy\ListOfValueSeeder;
use Database\Seeders\Dummy\ShopSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UserSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(SidebarSeeder::class);
    }
}