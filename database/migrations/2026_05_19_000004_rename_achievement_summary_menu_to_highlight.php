<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('menus')
            ->where('path', 'admin/page/achievement-summary/list')
            ->update([
                'name' => json_encode(['en' => 'Highlight', 'km' => 'គន្លឹះ']),
                'updated_at' => now(),
            ]);

        DB::table('module_permissions')
            ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(display_name, '$.en')) = 'Achievement Summary'")
            ->update([
                'display_name' => json_encode(['en' => 'Highlight', 'km' => 'គន្លឹះ']),
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        DB::table('menus')
            ->where('path', 'admin/page/achievement-summary/list')
            ->update([
                'name' => json_encode(['en' => 'Achievement Summary', 'km' => 'សេចក្តីសង្ខេបសមិទ្ធផល']),
                'updated_at' => now(),
            ]);

        DB::table('module_permissions')
            ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(display_name, '$.en')) = 'Highlight'")
            ->update([
                'display_name' => json_encode(['en' => 'Achievement Summary', 'km' => 'សេចក្តីសង្ខេបសមិទ្ធផល']),
                'updated_at' => now(),
            ]);
    }
};
