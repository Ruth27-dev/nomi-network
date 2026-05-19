<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Find the Page Management module permission parent
        $pageModule = DB::table('module_permissions')
            ->whereNull('parent_id')
            ->where(function ($q) {
                $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(display_name, '$.en')) = 'Page Management'")
                  ->orWhereRaw("display_name LIKE '%Page Management%'");
            })
            ->first();

        if (!$pageModule) {
            return;
        }

        // Add Our Impact module permission if not already present
        $exists = DB::table('permissions')
            ->where('name', 'our-impact-view')
            ->where('guard_name', 'admin')
            ->exists();

        if (!$exists) {
            $ourImpact = DB::table('module_permissions')->insertGetId([
                'parent_id'    => $pageModule->id,
                'display_name' => json_encode(['en' => 'Our Impact', 'km' => 'Our Impact']),
                'sort_no'      => 99,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);

            DB::table('permissions')->insert([
                [
                    'display_name' => json_encode(['en' => 'View Data Listing', 'km' => 'មើលបញ្ជីទិន្នន័យ']),
                    'name'         => 'our-impact-view',
                    'guard_name'   => 'admin',
                    'module_id'    => $ourImpact,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ],
                [
                    'display_name' => json_encode(['en' => 'Edit Data', 'km' => 'កែប្រែទិន្នន័យ']),
                    'name'         => 'our-impact-update',
                    'guard_name'   => 'admin',
                    'module_id'    => $ourImpact,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ],
            ]);
        }
    }

    public function down(): void
    {
        DB::table('permissions')
            ->whereIn('name', ['our-impact-view', 'our-impact-update'])
            ->where('guard_name', 'admin')
            ->delete();

        DB::table('module_permissions')
            ->whereRaw("display_name LIKE '%Our Impact%'")
            ->delete();
    }
};
