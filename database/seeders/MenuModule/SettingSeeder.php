<?php

namespace Database\Seeders\MenuModule;

use App\Models\Menu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class   SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //setting
        $setting = Menu::create([
            'name' => json_encode(['en' => 'Setting', 'km' => 'ការកំណត់']),
            'icon'  => 'settings',
            'active' => 'admin/setting/*',
            'ordering' => 3,
            'permission' => array('company-view','bank-account-view'),
        ]);

        Menu::create([
            'parent_id' => $setting->id,
            'name' => json_encode([
                'en' => 'Company',
                'km' => 'ក្រុមហ៊ុន',
            ]),
            'path' => 'admin/setting/company/list',
            'active' => 'admin/setting/company/*',
            'ordering' => 1,
            'permission' => array('company-view'),
        ]);
        Menu::create([
            'parent_id' => $setting->id,
            'name' => json_encode([
                'en' => 'Bank Account',
                'km' => 'គណនីធនាគារ',
            ]),
            'path' => 'admin/setting/bank-account/list',
            'active' => 'admin/setting/bank-account/*',
            'ordering' => 7,
            'permission' => array('bank-account-view'),
        ]);
    }
}
