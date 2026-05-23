<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('product_categories')) {
            return;
        }

        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['product_id', 'category_id']);
        });

        // Migrate existing category_id data into the pivot table
        DB::statement('
            INSERT INTO product_categories (product_id, category_id, created_at, updated_at)
            SELECT id, category_id, NOW(), NOW()
            FROM products
            WHERE category_id IS NOT NULL
        ');
    }

    public function down(): void
    {
        Schema::dropIfExists('product_categories');
    }
};
