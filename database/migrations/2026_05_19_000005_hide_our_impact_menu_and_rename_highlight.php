<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Hide the Our Impact page menu item
        DB::table('menus')
            ->where('path', 'admin/page/our-impact/list')
            ->update([
                'disabled_at' => now(),
                'updated_at'  => now(),
            ]);

        // Rename Highlight (achievement_summary) to Our Impact
        DB::table('menus')
            ->where('path', 'admin/page/achievement-summary/list')
            ->update([
                'name'       => json_encode(['en' => 'Our Impact', 'km' => 'ផលប៉ះពាល់របស់យើង']),
                'updated_at' => now(),
            ]);

        DB::table('module_permissions')
            ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(display_name, '$.en')) = 'Highlight'")
            ->update([
                'display_name' => json_encode(['en' => 'Our Impact', 'km' => 'ផលប៉ះពាល់របស់យើង']),
                'updated_at'   => now(),
            ]);
    }

    public function down(): void
    {
        DB::table('menus')
            ->where('path', 'admin/page/our-impact/list')
            ->update([
                'disabled_at' => null,
                'updated_at'  => now(),
            ]);

        DB::table('menus')
            ->where('path', 'admin/page/achievement-summary/list')
            ->update([
                'name'       => json_encode(['en' => 'Highlight', 'km' => 'គន្លឹះ']),
                'updated_at' => now(),
            ]);

        DB::table('module_permissions')
            ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(display_name, '$.en')) = 'Our Impact'")
            ->whereRaw("display_name NOT LIKE '%our-impact%'")
            ->update([
                'display_name' => json_encode(['en' => 'Highlight', 'km' => 'គន្លឹះ']),
                'updated_at'   => now(),
            ]);
    }
};
