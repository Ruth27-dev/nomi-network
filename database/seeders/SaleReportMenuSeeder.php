<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class SaleReportMenuSeeder extends Seeder
{
    public function run(): void
    {
        // Find the existing Orders parent menu
        $orderMenu = Menu::whereJsonContains('permission', 'order-view')
            ->whereNull('parent_id')
            ->where('active', 'admin/order/*')
            ->first();

        if (!$orderMenu) {
            $this->command->warn('Orders parent menu not found. Skipping Sale Report menu item.');
            return;
        }

        // Skip if already inserted
        $exists = Menu::where('parent_id', $orderMenu->id)
            ->where('path', 'admin/sale-report/list')
            ->exists();

        if ($exists) {
            $this->command->info('Sale Report menu item already exists. Skipping.');
            return;
        }

        Menu::create([
            'parent_id'  => $orderMenu->id,
            'name'       => json_encode(['en' => 'Sale Report', 'km' => 'របាយការណ៍លក់']),
            'path'       => 'admin/sale-report/list',
            'active'     => 'admin/sale-report/*',
            'ordering'   => 2,
            'permission' => ['order-view'],
        ]);

        $this->command->info('Sale Report menu item added successfully.');
    }
}
