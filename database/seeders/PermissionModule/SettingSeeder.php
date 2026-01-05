<?php

namespace Database\Seeders\PermissionModule;

use App\Models\ModulePermission;
use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $setting = ModulePermission::create([
            'display_name'  => json_encode([
                'en' => 'Setting',
                'km' => 'ការកំណត់',
            ]),
            'sort_no'       => 3,
        ]);

        $company = ModulePermission::create([
            'parent_id'     =>  $setting->id,
            'display_name'  => json_encode([
                'en' => 'Company',
                'km' => 'ក្រុមហ៊ុន',
            ]),
            'sort_no'       => $this->increaseIndex(),
        ]);

        Permission::insert([
            [
                'display_name'  => json_encode(config('permission_module.action.view')),
                'name'          => 'company-view',
                'guard_name'    => 'admin',
                'module_id'     => $company->id,
            ],
            [
                'display_name'  => json_encode(config('permission_module.action.update')),
                'name'          => 'company-update',
                'guard_name'    => 'admin',
                'module_id'     => $company->id,
            ],
        ]);



        $bankAccount = ModulePermission::create([
            'parent_id'     =>  $setting->id,
            'display_name'  => json_encode([
                'en' => 'Bank Account',
                'km' => 'គណនីធនាគារ',
            ]),
            'sort_no'       => $this->increaseIndex(),
        ]);

        Permission::insert([
            [
                'display_name'  => json_encode(config('permission_module.action.view')),
                'name'          => 'bank-account-view',
                'guard_name'    => 'admin',
                'module_id'     => $bankAccount->id,
            ],
            [
                'display_name'  => json_encode(config('permission_module.action.create')),
                'name'          => 'bank-account-create',
                'guard_name'    => 'admin',
                'module_id'     => $bankAccount->id,
            ],
            [
                'display_name'  => json_encode(config('permission_module.action.update')),
                'name'          => 'bank-account-update',
                'guard_name'    => 'admin',
                'module_id'     => $bankAccount->id,
            ],
            [
                'display_name'  => json_encode(config('permission_module.action.delete')),
                'name'          => 'bank-account-delete',
                'guard_name'    => 'admin',
                'module_id'     => $bankAccount->id,
            ],
            [
                'display_name'  => json_encode(config('permission_module.action.restore')),
                'name'          => 'bank-account-restore',
                'guard_name'    => 'admin',
                'module_id'     => $bankAccount->id,
            ],
        ]);

        $banner = ModulePermission::create([
            'parent_id'     =>  $setting->id,
            'display_name'  => json_encode(config('permission_module.menu.banner')),
            'sort_no'       => $this->increaseIndex(),
        ]);

        Permission::insert([
            [
                'display_name'  => json_encode(config('permission_module.action.view')),
                'name'          => 'banner-view',
                'guard_name'    => 'admin',
                'module_id'     => $banner->id,
            ],
            [
                'display_name'  => json_encode(config('permission_module.action.create')),
                'name'          => 'banner-create',
                'guard_name'    => 'admin',
                'module_id'     => $banner->id,
            ],
            [
                'display_name'  => json_encode(config('permission_module.action.update')),
                'name'          => 'banner-update',
                'guard_name'    => 'admin',
                'module_id'     => $banner->id,
            ],
            [
                'display_name'  => json_encode(config('permission_module.action.delete')),
                'name'          => 'banner-delete',
                'guard_name'    => 'admin',
                'module_id'     => $banner->id,
            ],
            [
                'display_name'  => json_encode(config('permission_module.action.restore')),
                'name'          => 'banner-restore',
                'guard_name'    => 'admin',
                'module_id'     => $banner->id,
            ],
        ]);
    }
    public $index = 0;
    public function increaseIndex()
    {
        return $this->index += 1;
    }
}
