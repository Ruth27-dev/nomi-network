<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('product_sources')) {
            return;
        }

        Schema::create('product_sources', function (Blueprint $table) {
            $table->id();
            $table->text('name_en');
            $table->text('name_kh')->nullable();
            $table->text('contact_info')->nullable();
            $table->timestamps();
            $table->boolean('is_active')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_sources');
    }
};
