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
                'display_name'  => json_encode(config('permission_module.action.view')),
                'name'          => 'category-view',
                'guard_name'    => 'admin',
                'module_id'     => $category->id,
            ],
            [
                'display_name'  => json_encode(config('permission_module.action.create')),
                'name'          => 'category-create',
                'guard_name'    => 'admin',
                'module_id'     => $category->id,
            ],
            [
                'display_name'  => json_encode(config('permission_module.action.update')),
                'name'          => 'category-update',
                'guard_name'    => 'admin',
                'module_id'     => $category->id,
            ],
            [
                'display_name'  => json_encode(config('permission_module.action.delete')),
                'name'          => 'category-delete',
                'guard_name'    => 'admin',
                'module_id'     => $category->id,
            ],
            [
                'display_name'  => json_encode(config('permission_module.action.restore')),
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
                'display_name'  => json_encode(config('permission_module.action.view')),
                'name'          => 'product-view',
                'guard_name'    => 'admin',
                'module_id'     => $products->id,
            ],
            [
                'display_name'  => json_encode(config('permission_module.action.create')),
                'name'          => 'product-create',
                'guard_name'    => 'admin',
                'module_id'     => $products->id,
            ],
            [
                'display_name'  => json_encode(config('permission_module.action.update')),
                'name'          => 'product-update',
                'guard_name'    => 'admin',
                'module_id'     => $products->id,
            ],
            [
                'display_name'  => json_encode(config('permission_module.action.delete')),
                'name'          => 'product-delete',
                'guard_name'    => 'admin',
                'module_id'     => $products->id,
            ],
            [
                'display_name'  => json_encode(config('permission_module.action.restore')),
                'name'          => 'product-restore',
                'guard_name'    => 'admin',
                'module_id'     => $products->id,
            ],
        ]);

        $productVariation = ModulePermission::create([
            'parent_id'     => $product->id,
            'display_name'  => json_encode([
                'en' => 'Product Variations',
                'km' => 'Product Variations',
            ]),
            'sort_no'       => $this->increaseIndex(),
        ]);

        Permission::insert([
            [
                'display_name'  => json_encode(config('permission_module.action.view')),
                'name'          => 'product-variation-view',
                'guard_name'    => 'admin',
                'module_id'     => $productVariation->id,
            ],
            [
                'display_name'  => json_encode(config('permission_module.action.create')),
                'name'          => 'product-variation-create',
                'guard_name'    => 'admin',
                'module_id'     => $productVariation->id,
            ],
            [
                'display_name'  => json_encode(config('permission_module.action.update')),
                'name'          => 'product-variation-update',
                'guard_name'    => 'admin',
                'module_id'     => $productVariation->id,
            ],
            [
                'display_name'  => json_encode(config('permission_module.action.delete')),
                'name'          => 'product-variation-delete',
                'guard_name'    => 'admin',
                'module_id'     => $productVariation->id,
            ],
        ]);

        $productDiscount = ModulePermission::create([
            'parent_id'     => $product->id,
            'display_name'  => json_encode([
                'en' => 'Product Discounts',
                'km' => 'Product Discounts',
            ]),
            'sort_no'       => $this->increaseIndex(),
        ]);

        Permission::insert([
            [
                'display_name'  => json_encode(config('permission_module.action.view')),
                'name'          => 'product-discount-view',
                'guard_name'    => 'admin',
                'module_id'     => $productDiscount->id,
            ],
            [
                'display_name'  => json_encode(config('permission_module.action.create')),
                'name'          => 'product-discount-create',
                'guard_name'    => 'admin',
                'module_id'     => $productDiscount->id,
            ],
            [
                'display_name'  => json_encode(config('permission_module.action.update')),
                'name'          => 'product-discount-update',
                'guard_name'    => 'admin',
                'module_id'     => $productDiscount->id,
            ],
            [
                'display_name'  => json_encode(config('permission_module.action.update-status')),
                'name'          => 'product-discount-update-status',
                'guard_name'    => 'admin',
                'module_id'     => $productDiscount->id,
            ],
            [
                'display_name'  => json_encode(config('permission_module.action.delete')),
                'name'          => 'product-discount-delete',
                'guard_name'    => 'admin',
                'module_id'     => $productDiscount->id,
            ],
            [
                'display_name'  => json_encode(config('permission_module.action.restore')),
                'name'          => 'product-discount-restore',
                'guard_name'    => 'admin',
                'module_id'     => $productDiscount->id,
            ],
        ]);
    }

    public $index = 0;
    public function increaseIndex()
    {
        return $this->index += 1;
    }
}
