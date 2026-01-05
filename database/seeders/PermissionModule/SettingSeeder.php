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
            'display_name'  => json_encode(config('permission_module.menu.setting')),
            'sort_no'       => 3,
        ]);

        $company = ModulePermission::create([
            'parent_id'     =>  $setting->id,
            'display_name'  => json_encode(config('permission_module.menu.company')),
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
            'display_name'  => json_encode(config('permission_module.menu.bank_account')),
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
    }
    public $index = 0;
    public function increaseIndex()
    {
        return $this->index += 1;
    }
}
