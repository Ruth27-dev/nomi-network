<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('menus')
            ->where('path', 'admin/page/upcoming-event/list')
            ->update([
                'name' => json_encode(['en' => 'Events', 'km' => 'ព្រឹត្តិការណ៍']),
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        DB::table('menus')
            ->where('path', 'admin/page/upcoming-event/list')
            ->update([
                'name' => json_encode(['en' => 'Upcoming Events', 'km' => 'ព្រឹត្តិការណ៍ខាងមុខ']),
                'updated_at' => now(),
            ]);
    }
};
