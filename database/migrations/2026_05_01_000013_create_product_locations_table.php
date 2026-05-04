<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('product_locations')) {
            return;
        }

        Schema::create('product_locations', function (Blueprint $table) {
            $table->id();
            $table->text('name_en');
            $table->string('location_type', 50)->default('province');
            $table->text('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        try {
            DB::statement("ALTER TABLE product_locations ADD CONSTRAINT product_locations_location_type_check CHECK (location_type IN ('province', 'city', 'district', 'other'))");
        } catch (\Throwable $e) {
            // Ignore if constraint already exists.
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('product_locations');
    }
};
