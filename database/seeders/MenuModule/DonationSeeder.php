<?php

namespace Database\Seeders\MenuModule;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class DonationSeeder extends Seeder
{
    public function run(): void
    {
        $donation = Menu::create([
            'name'       => json_encode(['en' => 'Donations', 'km' => 'ការបរិច្ចាគ']),
            'icon'       => 'volunteer_activism',
            'active'     => 'admin/donation/*',
            'ordering'   => 3,
            'permission' => ['donation-view'],
        ]);

        Menu::create([
            'parent_id'  => $donation->id,
            'name'       => json_encode(['en' => 'Donations', 'km' => 'ការបរិច្ចាគ']),
            'path'       => 'admin/donation/list',
            'active'     => 'admin/donation/list*',
            'ordering'   => 1,
            'permission' => ['donation-view'],
        ]);
    }
}
