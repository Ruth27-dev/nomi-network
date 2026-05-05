<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('products')) {
            return;
        }

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('sku', 255)->nullable()->unique();
            $table->text('name_en');
            $table->text('name_kh')->nullable();
            $table->text('description_en')->nullable();
            $table->text('description_kh')->nullable();
            $table->decimal('price', 14, 2)->default(0);
            $table->integer('stock')->default(0);
            $table->boolean('is_preorder')->default(false);
            $table->boolean('has_variation')->default(false);
            $table->boolean('is_active')->default(true);
            $table->foreignId('product_source_id')->nullable()->constrained('product_sources')->nullOnDelete();
            $table->foreignId('product_location_id')->nullable()->constrained('product_locations')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
