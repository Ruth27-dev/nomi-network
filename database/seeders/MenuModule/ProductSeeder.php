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
            'permission' => array('category-view','product-view','product-attribute-view','product-discount-view','product-location-view','product-stock-view'),
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
                'en'    => "Product Attributes",
                'km'    => "លក្ខណៈផលិតផល",
            ]),
            'path'          => 'admin/product/attribute/list',
            'active'        => 'admin/product/attribute/list*',
            'ordering'      => 4,
            'permission'    => array('product-attribute-view'),
        ]);
        Menu::create([
            'parent_id' => $product->id,
            'name'      => json_encode([
                'en'    => "Discounts",
                'km'    => "Discounts",
            ]),
            'path'          => 'admin/product/discount/list',
            'active'        => 'admin/product/discount/list*',
            'ordering'      => 5,
            'permission'    => array('product-discount-view'),
        ]);
        Menu::create([
            'parent_id' => $product->id,
            'name'      => json_encode([
                'en'    => "Product Locations",
                'km'    => "ទីតាំងផលិតផល",
            ]),
            'path'          => 'admin/product/location/list',
            'active'        => 'admin/product/location/list*',
            'ordering'      => 6,
            'permission'    => array('product-location-view'),
        ]);
        Menu::create([
            'parent_id' => $product->id,
            'name'      => json_encode([
                'en'    => "Stock Inventory",
                'km'    => "ស្តុក",
            ]),
            'path'          => 'admin/product/stock/list',
            'active'        => 'admin/product/stock/list*',
            'ordering'      => 7,
            'permission'    => array('product-stock-view'),
        ]);
    }
}
