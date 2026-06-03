<?php

namespace Database\Seeders\PermissionModule;

use App\Models\ModulePermission;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class DonationSeeder extends Seeder
{
    public function run(): void
    {
        $donation = ModulePermission::create([
            'display_name' => json_encode(['en' => 'Donation', 'km' => 'ការបរិច្ចាគ']),
            'sort_no'      => 3,
        ]);

        $donationItem = ModulePermission::create([
            'parent_id'    => $donation->id,
            'display_name' => json_encode(['en' => 'Donations', 'km' => 'ការបរិច្ចាគ']),
            'sort_no'      => 1,
        ]);

        Permission::insert([
            [
                'display_name' => json_encode(config('permission_module.action.view')),
                'name'         => 'donation-view',
                'guard_name'   => 'admin',
                'module_id'    => $donationItem->id,
            ],
        ]);
    }
}
