<?php

namespace Database\Seeders\PermissionModule;

use App\Models\ModulePermission;
use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $page = ModulePermission::create([
            'display_name'  => json_encode(config('permission_module.menu.page_management')),
            'sort_no'       => 12,
        ]);

        $banner = ModulePermission::create([
            'parent_id'     =>   $page->id,
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

        $achievementSummary = ModulePermission::create([
            'parent_id'     =>   $page->id,
            'display_name'  => json_encode(config('permission_module.menu.achievement_summary')),
            'sort_no'       => $this->increaseIndex(),
        ]);

        Permission::insert([
            [
                'display_name'  => json_encode(config('permission_module.action.view')),
                'name'          => 'achievement-summary-view',
                'guard_name'    => 'admin',
                'module_id'     => $achievementSummary->id,
            ],
            [
                'display_name'  => json_encode(config('permission_module.action.create')),
                'name'          => 'achievement-summary-create',
                'guard_name'    => 'admin',
                'module_id'     => $achievementSummary->id,
            ],
            [
                'display_name'  => json_encode(config('permission_module.action.update')),
                'name'          => 'achievement-summary-update',
                'guard_name'    => 'admin',
                'module_id'     => $achievementSummary->id,
            ],
            [
                'display_name'  => json_encode(config('permission_module.action.delete')),
                'name'          => 'achievement-summary-delete',
                'guard_name'    => 'admin',
                'module_id'     => $achievementSummary->id,
            ],
            [
                'display_name'  => json_encode(config('permission_module.action.restore')),
                'name'          => 'achievement-summary-restore',
                'guard_name'    => 'admin',
                'module_id'     => $achievementSummary->id,
            ],
        ]);

        $ourProgram = ModulePermission::create([
            'parent_id'     =>   $page->id,
            'display_name'  => json_encode(config('permission_module.menu.our_program')),
            'sort_no'       => $this->increaseIndex(),
        ]);

        Permission::insert([
            [
                'display_name'  => json_encode(config('permission_module.action.view')),
                'name'          => 'our-program-view',
                'guard_name'    => 'admin',
                'module_id'     => $ourProgram->id,
            ],
            [
                'display_name'  => json_encode(config('permission_module.action.update')),
                'name'          => 'our-program-update',
                'guard_name'    => 'admin',
                'module_id'     => $ourProgram->id,
            ],
        ]);

        $privacyPolicy = ModulePermission::create([
            'parent_id'     =>   $page->id,
            'display_name'  => json_encode(config('permission_module.menu.privacy_policy')),
            'sort_no'       => $this->increaseIndex(),
        ]);

        Permission::insert([
            [
                'display_name'  => json_encode(config('permission_module.action.view')),
                'name'          => 'privacy-policy-view',
                'guard_name'    => 'admin',
                'module_id'     => $privacyPolicy->id,
            ],
            [
                'display_name'  => json_encode(config('permission_module.action.update')),
                'name'          => 'privacy-policy-update',
                'guard_name'    => 'admin',
                'module_id'     => $privacyPolicy->id,
            ],
        ]);

        $contactUs = ModulePermission::create([
            'parent_id'     =>   $page->id,
            'display_name'  => json_encode(config('permission_module.menu.contact_us')),
            'sort_no'       => $this->increaseIndex(),
        ]);

        Permission::insert([
            [
                'display_name'  => json_encode(config('permission_module.action.view')),
                'name'          => 'contact-us-view',
                'guard_name'    => 'admin',
                'module_id'     => $contactUs->id,
            ],
            [
                'display_name'  => json_encode(config('permission_module.action.update')),
                'name'          => 'contact-us-update',
                'guard_name'    => 'admin',
                'module_id'     => $contactUs->id,
            ],
        ]);
        $aboutUs = ModulePermission::create([
            'parent_id'     =>   $page->id,
            'display_name'  => json_encode(config('permission_module.menu.about_us')),
            'sort_no'       => $this->increaseIndex(),
        ]);

        Permission::insert([
            [
                'display_name'  => json_encode(config('permission_module.action.view')),
                'name'          => 'about-us-view',
                'guard_name'    => 'admin',
                'module_id'     => $aboutUs->id,
            ],
            [
                'display_name'  => json_encode(config('permission_module.action.update')),
                'name'          => 'about-us-update',
                'guard_name'    => 'admin',
                'module_id'     => $aboutUs->id,
            ],
        ]);

        $whyChooseUs = ModulePermission::create([
            'parent_id'     =>   $page->id,
            'display_name'  => json_encode(config('permission_module.menu.why_choose_us')),
            'sort_no'       => $this->increaseIndex(),
        ]);

        Permission::insert([
            [
                'display_name'  => json_encode(config('permission_module.action.view')),
                'name'          => 'why-choose-us-view',
                'guard_name'    => 'admin',
                'module_id'     => $whyChooseUs->id,
            ],
            [
                'display_name'  => json_encode(config('permission_module.action.update')),
                'name'          => 'why-choose-us-update',
                'guard_name'    => 'admin',
                'module_id'     => $whyChooseUs->id,
            ],
        ]);

        $ourMission = ModulePermission::create([
            'parent_id'     =>   $page->id,
            'display_name'  => json_encode(config('permission_module.menu.our_mission')),
            'sort_no'       => $this->increaseIndex(),
        ]);

        Permission::insert([
            [
                'display_name'  => json_encode(config('permission_module.action.view')),
                'name'          => 'our-mission-view',
                'guard_name'    => 'admin',
                'module_id'     => $ourMission->id,
            ],
            [
                'display_name'  => json_encode(config('permission_module.action.update')),
                'name'          => 'our-mission-update',
                'guard_name'    => 'admin',
                'module_id'     => $ourMission->id,
            ],
        ]);


        $ourStory = ModulePermission::create([
            'parent_id'     =>   $page->id,
            'display_name'  => json_encode(config('permission_module.menu.our_story')),
            'sort_no'       => $this->increaseIndex(),
        ]);

        Permission::insert([
            [
                'display_name'  => json_encode(config('permission_module.action.view')),
                'name'          => 'our-story-view',
                'guard_name'    => 'admin',
                'module_id'     => $ourStory->id,
            ],
            [
                'display_name'  => json_encode(config('permission_module.action.update')),
                'name'          => 'our-story-update',
                'guard_name'    => 'admin',
                'module_id'     => $ourStory->id,
            ],
        ]);

        $ourTeam = ModulePermission::create([
            'parent_id'     =>   $page->id,
            'display_name'  => json_encode(config('permission_module.menu.our_team')),
            'sort_no'       => $this->increaseIndex(),
        ]);

        Permission::insert([
            [
                'display_name'  => json_encode(config('permission_module.action.view')),
                'name'          => 'our-team-view',
                'guard_name'    => 'admin',
                'module_id'     => $ourTeam->id,
            ],
            [
                'display_name'  => json_encode(config('permission_module.action.update')),
                'name'          => 'our-team-update',
                'guard_name'    => 'admin',
                'module_id'     => $ourTeam->id,
            ],
        ]);

        $FrequentlyAskedQuestion = ModulePermission::create([
            'parent_id'     =>   $page->id,
            'display_name'  => json_encode(config('permission_module.menu.frequently_asked_question')),
            'sort_no'       => $this->increaseIndex(),
        ]);

        Permission::insert([
            [
                'display_name'  => json_encode(config('permission_module.action.view')),
                'name'          => 'frequently-asked-question-view',
                'guard_name'    => 'admin',
                'module_id'     => $FrequentlyAskedQuestion->id,
            ],
            [
                'display_name'  => json_encode(config('permission_module.action.update')),
                'name'          => 'frequently-asked-question-update',
                'guard_name'    => 'admin',
                'module_id'     => $FrequentlyAskedQuestion->id,
            ],
        ]);
    }

    public $index = 0;
    public function increaseIndex()
    {
        return $this->index += 1;
    }
}
