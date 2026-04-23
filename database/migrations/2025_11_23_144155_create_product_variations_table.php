<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_variations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('product_id');
            $table->json('title')->nullable()->comment('multi language');
            $table->string('status')->nullable()->comment('ACTIVE, INACTIVE');
            $table->double('price')->nullable();
            $table->string('size',255)->nullable();
            $table->json('description')->nullable()->comment('multi language');
            $table->json('note')->nullable();
            $table->boolean('is_available')->nullable()->default(false);
            $table->string('image', 255)->nullable();
            $table->bigInteger('user_id')->nullable()->comment('last performed');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variations');
    }
};
