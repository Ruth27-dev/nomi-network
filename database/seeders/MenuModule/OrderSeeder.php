<?php

namespace Database\Seeders\MenuModule;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $order = Menu::create([
            'name' => json_encode(['en' => 'Orders', 'km' => 'ការបញ្ជាទិញ']),
            'icon'  => 'receipt_long',
            'active' => 'admin/order/*',
            'ordering' => 2,
            'permission' => array('order-view'),
        ]);

        Menu::create([
            'parent_id' => $order->id,
            'name'      => json_encode([
                'en'    => "Orders",
                'km'    => "ការបញ្ជាទិញ",
            ]),
            'path'          => 'admin/order/list',
            'active'        => 'admin/order/list*',
            'ordering'      => 1,
            'permission'    => array('order-view'),
        ]);
    }
}
