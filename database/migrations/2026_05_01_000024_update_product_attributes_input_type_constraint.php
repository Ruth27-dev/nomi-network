<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('product_attributes')) {
            return;
        }

        $driver = DB::getDriverName();

        try {
            if ($driver === 'pgsql') {
                DB::statement('ALTER TABLE product_attributes DROP CONSTRAINT IF EXISTS product_attributes_input_type_check');
                DB::statement("ALTER TABLE product_attributes ADD CONSTRAINT product_attributes_input_type_check CHECK (input_type IN ('select', 'text', 'number', 'color', 'size', 'button'))");
            } elseif ($driver === 'mysql') {
                DB::statement('ALTER TABLE product_attributes DROP CHECK product_attributes_input_type_check');
                DB::statement("ALTER TABLE product_attributes ADD CONSTRAINT product_attributes_input_type_check CHECK (input_type IN ('select', 'text', 'number', 'color', 'size', 'button'))");
            }
        } catch (\Throwable $e) {
            // Keep migration non-blocking if check constraints are unsupported or named differently.
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('product_attributes')) {
            return;
        }

        $driver = DB::getDriverName();

        try {
            if ($driver === 'pgsql') {
                DB::statement('ALTER TABLE product_attributes DROP CONSTRAINT IF EXISTS product_attributes_input_type_check');
                DB::statement("ALTER TABLE product_attributes ADD CONSTRAINT product_attributes_input_type_check CHECK (input_type IN ('select', 'text', 'number', 'color'))");
            } elseif ($driver === 'mysql') {
                DB::statement('ALTER TABLE product_attributes DROP CHECK product_attributes_input_type_check');
                DB::statement("ALTER TABLE product_attributes ADD CONSTRAINT product_attributes_input_type_check CHECK (input_type IN ('select', 'text', 'number', 'color'))");
            }
        } catch (\Throwable $e) {
            // Keep rollback non-blocking.
        }
    }
};
