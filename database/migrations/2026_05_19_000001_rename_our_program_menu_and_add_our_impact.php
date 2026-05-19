<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Rename "Our Programs" → "What We Do" in menus table
        DB::table('menus')
            ->where('path', 'admin/page/our-program/list')
            ->update([
                'name' => json_encode(['en' => 'What We Do', 'km' => 'What We Do']),
            ]);

        // Find Page Management parent menu
        $parent = DB::table('menus')
            ->where('active', 'admin/page/*')
            ->whereNull('parent_id')
            ->first();

        if (!$parent) {
            return;
        }

        // Add "Our Impact" menu under Page Management if not already present
        $exists = DB::table('menus')
            ->where('path', 'admin/page/our-impact/list')
            ->exists();

        if (!$exists) {
            DB::table('menus')->insert([
                'parent_id'  => $parent->id,
                'name'       => json_encode(['en' => 'Our Impact', 'km' => 'Our Impact']),
                'path'       => 'admin/page/our-impact/list',
                'active'     => 'admin/page/our-impact/*',
                'ordering'   => 16,
                'permission' => json_encode(['our-impact-view']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Update parent permission list to include our-impact-view
            $currentPermissions = json_decode($parent->permission, true) ?? [];
            if (!in_array('our-impact-view', $currentPermissions)) {
                $currentPermissions[] = 'our-impact-view';
                DB::table('menus')
                    ->where('id', $parent->id)
                    ->update(['permission' => json_encode($currentPermissions)]);
            }
        }
    }

    public function down(): void
    {
        // Revert "What We Do" back to "Our Programs"
        DB::table('menus')
            ->where('path', 'admin/page/our-program/list')
            ->update([
                'name' => json_encode(['en' => 'Our Programs', 'km' => 'កម្មវិធីរបស់យើង']),
            ]);

        // Remove "Our Impact" menu
        DB::table('menus')
            ->where('path', 'admin/page/our-impact/list')
            ->delete();
    }
};
