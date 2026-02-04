<?php

namespace Database\Seeders\MenuModule;

use App\Models\Menu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //page management
        $page = Menu::create([
            'name' => json_encode(['en' => 'Page Management', 'km' => 'ការគ្រប់គ្រងទំព័រ']),
            'icon'  => 'folder',
            'active' => 'admin/page/*',
            'ordering' => 10,
            'permission' => array('privacy-policy-view', 'contact-us-view', 'why-choose-us-view', 'our-mission-view', 'frequently-asked-question-view', 'about-us-view','our-team-view','our-story-view'),
        ]);

        Menu::create([
            'parent_id' => $page->id,
            'name' => json_encode([
                'en' => 'Privacy Policy',
                'km' => 'គោលការណ៍ឯក ជនភាព',
            ]),
            'path' => 'admin/page/privacy-policy/list',
            'active' => 'admin/page/privacy-policy/*',
            'ordering' => 1,
            'permission' => array('privacy-policy-view'),
        ]);

        Menu::create([
            'parent_id' => $page->id,
            'name' => json_encode([
                'en' => 'Contact Us',
                'km' => 'ទំនាក់ទំនង',
            ]),
            'path' => 'admin/page/contact-us/list',
            'active' => 'admin/page/contact-us/*',
            'ordering' => 2,
            'permission' => array('contact-us-view'),
        ]);

        Menu::create([
            'parent_id' => $page->id,
            'name' => json_encode([
                'en' => 'About Us',
                'km' => 'អំពីពួកយើង',
            ]),
            'path' => 'admin/page/about-us/list',
            'active' => 'admin/page/about-us/*',
            'ordering' => 9,
            'permission' => array('about-us-view'),
        ]);


        Menu::create([
            'parent_id' => $page->id,
            'name' => json_encode([
                'en' => 'Why Choose Us',
                'km' => 'ហេតុអ្វីជ្រើសរើសយើង',
            ]),
            'path' => 'admin/page/why-choose-us/list',
            'active' => 'admin/page/why-choose-us/*',
            'ordering' => 2,
            'permission' => array('why-choose-us-view'),
        ]);

        Menu::create([
            'parent_id' => $page->id,
            'name' => json_encode([
                'en' => 'Our Mission',
                'km' => 'បេសកកម្មរបស់យើង',
            ]),
            'path' => 'admin/page/our-mission/list',
            'active' => 'admin/page/our-mission/*',
            'ordering' => 2,
            'permission' => array('our-mission-view'),
        ]);

        Menu::create([
            'parent_id' => $page->id,
            'name' => json_encode([
                'en' => 'Our Story',
                'km' => 'ប្រវត្តិ​របស់យើង',
            ]),
            'path' => 'admin/page/our-story/list',
            'active' => 'admin/page/our-story/*',
            'ordering' => 2,
            'permission' => array('our-story-view'),
        ]);

        Menu::create([
            'parent_id' => $page->id,
            'name' => json_encode([
                'en' => 'Our Team',
                'km' => 'ក្រុមរបស់យើង',
            ]),
            'path' => 'admin/page/our-team/list',
            'active' => 'admin/page/our-team/*',
            'ordering' => 2,
            'permission' => array('our-team-view'),
        ]);

        Menu::create([
            'parent_id' => $page->id,
            'name' => json_encode([
                'en' => 'Frequently Asked Question',
                'km' => 'សំណួរដែលសួរញឹកញាប់',
            ]),
            'path' => 'admin/page/frequently-asked-question/list',
            'active' => 'admin/page/frequently-asked-question/*',
            'ordering' => 2,
            'permission' => array('frequently-asked-question-view'),
        ]);
    }
}
