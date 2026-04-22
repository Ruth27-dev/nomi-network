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
            'permission' => array('banner-view', 'achievement-summary-view', 'our-program-view', 'upcoming-event-view', 'our-core-value-view', 'mission-vision-view', 'social-media-view', 'privacy-policy-view', 'contact-us-view', 'why-choose-us-view', 'our-mission-view', 'frequently-asked-question-view', 'about-us-view', 'our-team-view', 'our-story-view'),
        ]);

        Menu::create([
            'parent_id' => $page->id,
            'name' => json_encode([
                'en' => 'Banner',
                'km' => 'Banner',
            ]),
            'path' => 'admin/page/banner/list',
            'active' => 'admin/page/banner/*',
            'ordering' => 1,
            'permission' => array('banner-view'),
        ]);

        Menu::create([
            'parent_id' => $page->id,
            'name' => json_encode([
                'en' => 'Achievement Summary',
                'km' => 'សេចក្តីសង្ខេបសមិទ្ធផល',
            ]),
            'path' => 'admin/page/achievement-summary/list',
            'active' => 'admin/page/achievement-summary/*',
            'ordering' => 2,
            'permission' => array('achievement-summary-view'),
        ]);

        Menu::create([
            'parent_id' => $page->id,
            'name' => json_encode([
                'en' => 'Our Programs',
                'km' => 'កម្មវិធីរបស់យើង',
            ]),
            'path' => 'admin/page/our-program/list',
            'active' => 'admin/page/our-program/*',
            'ordering' => 3,
            'permission' => array('our-program-view'),
        ]);

        Menu::create([
            'parent_id' => $page->id,
            'name' => json_encode([
                'en' => 'Upcoming Events',
                'km' => 'ព្រឹត្តិការណ៍ខាងមុខ',
            ]),
            'path' => 'admin/page/upcoming-event/list',
            'active' => 'admin/page/upcoming-event/*',
            'ordering' => 4,
            'permission' => array('upcoming-event-view'),
        ]);

        Menu::create([
            'parent_id' => $page->id,
            'name' => json_encode([
                'en' => 'Our Core Values',
                'km' => 'គុណតម្លៃស្នូលរបស់យើង',
            ]),
            'path' => 'admin/page/our-core-value/list',
            'active' => 'admin/page/our-core-value/*',
            'ordering' => 6,
            'permission' => array('our-core-value-view'),
        ]);

        Menu::create([
            'parent_id' => $page->id,
            'name' => json_encode([
                'en' => 'Our Story',
                'km' => 'ប្រវត្តិ​របស់យើង',
            ]),
            'path' => 'admin/page/our-story/list',
            'active' => 'admin/page/our-story/*',
            'ordering' => 8,
            'permission' => array('our-story-view'),
        ]);

        Menu::create([
            'parent_id' => $page->id,
            'name' => json_encode([
                'en' => 'Mission & Vision',
                'km' => 'បេសកកម្ម និងចក្ខុវិស័យ',
            ]),
            'path' => 'admin/page/mission-vision/list',
            'active' => 'admin/page/mission-vision/*',
            'ordering' => 5,
            'permission' => array('mission-vision-view'),
        ]);

        Menu::create([
            'parent_id' => $page->id,
            'name' => json_encode([
                'en' => 'Social Media',
                'km' => 'ប្រព័ន្ធផ្សព្វផ្សាយសង្គម',
            ]),
            'path' => 'admin/page/social-media/list',
            'active' => 'admin/page/social-media/*',
            'ordering' => 7,
            'permission' => array('social-media-view'),
        ]);

        Menu::create([
            'parent_id' => $page->id,
            'name' => json_encode([
                'en' => 'Privacy Policy',
                'km' => 'គោលការណ៍ឯក ជនភាព',
            ]),
            'path' => 'admin/page/privacy-policy/list',
            'active' => 'admin/page/privacy-policy/*',
            'ordering' => 9,
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
            'ordering' => 10,
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
            'ordering' => 11,
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
            'ordering' => 12,
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
            'ordering' => 13,
            'permission' => array('our-mission-view'),
        ]);

        Menu::create([
            'parent_id' => $page->id,
            'name' => json_encode([
                'en' => 'Our Team',
                'km' => 'ក្រុមរបស់យើង',
            ]),
            'path' => 'admin/page/our-team/list',
            'active' => 'admin/page/our-team/*',
            'ordering' => 14,
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
            'ordering' => 15,
            'permission' => array('frequently-asked-question-view'),
        ]);
    }
}
