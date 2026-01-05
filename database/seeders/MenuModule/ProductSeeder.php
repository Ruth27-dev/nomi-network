<?php

namespace Database\Seeders\MenuModule;

use App\Models\Menu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //product
        $product = Menu::create([
            'name' => json_encode(['en' => 'Products', 'km' => 'ផលិតផល']),
            'icon'  => 'inventory',
            'active' => 'admin/product/*',
            'ordering' => 1,
            'permission' => array('category-view','product-view'),
        ]);

        Menu::create([
            'parent_id' => $product->id,
            'name'      => json_encode([
                'en'    => "Categories",
                'km'    => "ប្រភេទ",
            ]),
            'path'          => 'admin/product/category/list',
            'active'        => 'admin/product/category/list*',
            'ordering'      => 2,
            'permission'    => array('category-view'),
        ]);
        Menu::create([
            'parent_id' => $product->id,
            'name'      => json_encode([
                'en'    => "Products",
                'km'    => "ផលិតផល",
            ]),
            'path'          => 'admin/product/product/list',
            'active'        => 'admin/product/product/list*',
            'ordering'      => 2,
            'permission'    => array('product-view'),
        ]);
        Menu::create([
            'parent_id' => $product->id,
            'name'      => json_encode([
                'en'    => "Product Variations",
                'km'    => "Product Variations",
            ]),
            'path'          => 'admin/product/variation/list',
            'active'        => 'admin/product/variation/list*',
            'ordering'      => 3,
            'permission'    => array('product-view'),
        ]);
    }
}
