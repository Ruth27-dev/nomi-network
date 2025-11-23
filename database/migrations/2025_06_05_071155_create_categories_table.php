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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('parent_id')->nullable()->comment('category id');
            $table->string('code', 255)->nullable();
            $table->json('title')->nullable()->comment('multi language');
            $table->json('description')->nullable()->comment('multi language');
            $table->string('image', 255)->nullable();
            $table->bigInteger('sequence')->nullable()->comment('ordering');
            $table->string('status')->nullable()->comment('ACTIVE, INACTIVE');
            $table->string('slug',255)->nullable();
            $table->bigInteger('user_id')->nullable()->comment('last performed');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
