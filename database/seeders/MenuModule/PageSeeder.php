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
            'permission' => array('banner-view', 'achievement-summary-view', 'our-program-view', 'production-view', 'upcoming-event-view', 'mission-vision-view', 'our-core-value-view', 'career-view', 'report-document-category-view', 'report-document-view', 'social-media-view', 'privacy-policy-view', 'contact-us-view', 'about-us-view', 'our-story-view'),
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
                'en' => 'Production',
                'km' => 'ផលិតកម្ម',
            ]),
            'path' => 'admin/page/production/list',
            'active' => 'admin/page/production/*',
            'ordering' => 15,
            'permission' => array('production-view'),
        ]);

        Menu::create([
            'parent_id' => $page->id,
            'name' => json_encode([
                'en' => 'Our Core Values',
                'km' => 'គុណតម្លៃស្នូលរបស់យើង',
            ]),
            'path' => 'admin/page/our-core-value/list',
            'active' => 'admin/page/our-core-value/*',
            'ordering' => 8,
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
            'ordering' => 6,
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
            'ordering' => 7,
            'permission' => array('mission-vision-view'),
        ]);

        Menu::create([
            'parent_id' => $page->id,
            'name' => json_encode([
                'en' => 'Careers',
                'km' => 'អាជីព',
            ]),
            'path' => 'admin/page/career/list',
            'active' => 'admin/page/career/*',
            'ordering' => 9,
            'permission' => array('career-view'),
        ]);

        Menu::create([
            'parent_id' => $page->id,
            'name' => json_encode([
                'en' => 'Social Media',
                'km' => 'ប្រព័ន្ធផ្សព្វផ្សាយសង្គម',
            ]),
            'path' => 'admin/page/social-media/list',
            'active' => 'admin/page/social-media/*',
            'ordering' => 5,
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
            'ordering' => 10,
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
            'ordering' => 11,
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
            'ordering' => 12,
            'permission' => array('about-us-view'),
        ]);


        Menu::create([
            'parent_id' => $page->id,
            'name' => json_encode([
                'en' => 'Reports & Documents Category',
                'km' => 'ប្រភេទរបាយការណ៍ និងឯកសារ',
            ]),
            'path' => 'admin/page/report-document-category/list',
            'active' => 'admin/page/report-document-category/*',
            'ordering' => 13,
            'permission' => array('report-document-category-view'),
        ]);

        Menu::create([
            'parent_id' => $page->id,
            'name' => json_encode([
                'en' => 'Reports & Documents',
                'km' => 'របាយការណ៍ និងឯកសារ',
            ]),
            'path' => 'admin/page/report-document/list',
            'active' => 'admin/page/report-document/*',
            'ordering' => 14,
            'permission' => array('report-document-view'),
        ]);
    }
}
