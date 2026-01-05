<?php

namespace Database\Seeders\PermissionModule;

use App\Models\ModulePermission;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $product = ModulePermission::create([
            'display_name'  => json_encode([
                'en' => 'Product',
                'km' => 'ផលិតផល',
            ]),
            'sort_no'       => 1,
        ]);

        $category = ModulePermission::create([
            'parent_id'     => $product->id,
            'display_name'  => json_encode([
                'en' => 'Category',
                'km' => 'ប្រភេទ',
            ]),
            'sort_no'       => $this->increaseIndex(),
        ]);

        Permission::insert([
            [
                'display_name'  => json_encode([
                    'en' => 'View Data Listing',
                    'km' => 'មើលបញ្ជីទិន្នន័យ',
                ]),
                'name'          => 'category-view',
                'guard_name'    => 'admin',
                'module_id'     => $category->id,
            ],
            [
                'display_name'  => json_encode([
                    'en' => 'Create Data',
                    'km' => 'បង្កើតទិន្នន័យ',
                ]),
                'name'          => 'category-create',
                'guard_name'    => 'admin',
                'module_id'     => $category->id,
            ],
            [
                'display_name'  => json_encode([
                    'en' => 'Edit Data',
                    'km' => 'កែប្រែទិន្នន័យ',
                ]),
                'name'          => 'category-update',
                'guard_name'    => 'admin',
                'module_id'     => $category->id,
            ],
            [
                'display_name'  => json_encode([
                    'en' => 'Move Data To Trash',
                    'km' => 'ដាក់ទិន្នន័យ​ក្នុង​ធុងសំរាម',
                ]),
                'name'          => 'category-delete',
                'guard_name'    => 'admin',
                'module_id'     => $category->id,
            ],
            [
                'display_name'  => json_encode([
                    'en' => 'Restore Data',
                    'km' => 'ស្ដារទិន្នន័យឡើងវិញ',
                ]),
                'name'          => 'category-restore',
                'guard_name'    => 'admin',
                'module_id'     => $category->id,
            ],
        ]);

        $products = ModulePermission::create([
            'parent_id'     => $product->id,
            'display_name'  => json_encode([
                'en' => 'Products',
                'km' => 'ផលិតផល',
            ]),
            'sort_no'       => $this->increaseIndex(),
        ]);

        Permission::insert([
            [
                'display_name'  => json_encode([
                    'en' => 'View Data Listing',
                    'km' => 'មើលបញ្ជីទិន្នន័យ',
                ]),
                'name'          => 'product-view',
                'guard_name'    => 'admin',
                'module_id'     => $products->id,
            ],
            [
                'display_name'  => json_encode([
                    'en' => 'Create Data',
                    'km' => 'បង្កើតទិន្នន័យ',
                ]),
                'name'          => 'product-create',
                'guard_name'    => 'admin',
                'module_id'     => $products->id,
            ],
            [
                'display_name'  => json_encode([
                    'en' => 'Edit Data',
                    'km' => 'កែប្រែទិន្នន័យ',
                ]),
                'name'          => 'product-update',
                'guard_name'    => 'admin',
                'module_id'     => $products->id,
            ],
            [
                'display_name'  => json_encode([
                    'en' => 'Move Data To Trash',
                    'km' => 'ដាក់ទិន្នន័យ​ក្នុង​ធុងសំរាម',
                ]),
                'name'          => 'product-delete',
                'guard_name'    => 'admin',
                'module_id'     => $products->id,
            ],
            [
                'display_name'  => json_encode([
                    'en' => 'Restore Data',
                    'km' => 'ស្ដារទិន្នន័យឡើងវិញ',
                ]),
                'name'          => 'product-restore',
                'guard_name'    => 'admin',
                'module_id'     => $products->id,
            ],
        ]);
    }

    public $index = 0;
    public function increaseIndex()
    {
        return $this->index += 1;
    }
}
