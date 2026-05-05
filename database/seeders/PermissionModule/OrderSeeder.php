<?php

namespace Database\Seeders\PermissionModule;

use App\Models\ModulePermission;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $order = ModulePermission::create([
            'display_name'  => json_encode([
                'en' => 'Order',
                'km' => 'ការបញ្ជាទិញ',
            ]),
            'sort_no'       => 2,
        ]);

        $orderItem = ModulePermission::create([
            'parent_id'     => $order->id,
            'display_name'  => json_encode([
                'en' => 'Orders',
                'km' => 'ការបញ្ជាទិញ',
            ]),
            'sort_no'       => 1,
        ]);

        Permission::insert([
            [
                'display_name'  => json_encode(config('permission_module.action.view')),
                'name'          => 'order-view',
                'guard_name'    => 'admin',
                'module_id'     => $orderItem->id,
            ],
            [
                'display_name'  => json_encode(config('permission_module.action.update')),
                'name'          => 'order-update',
                'guard_name'    => 'admin',
                'module_id'     => $orderItem->id,
            ],
        ]);
    }
}
