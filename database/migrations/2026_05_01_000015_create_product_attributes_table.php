<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('product_attributes')) {
            return;
        }

        Schema::create('product_attributes', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->string('code', 255)->unique();
            $table->string('input_type', 50)->default('select');
            $table->boolean('is_variation')->default(true);
            $table->timestamps();
            $table->boolean('is_active')->default(true);
        });

        try {
            DB::statement("ALTER TABLE product_attributes ADD CONSTRAINT product_attributes_input_type_check CHECK (input_type IN ('select', 'text', 'number', 'color', 'size', 'button'))");
        } catch (\Throwable $e) {
            // Ignore if constraint already exists.
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('product_attributes');
    }
};
