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
        Schema::create('galleries', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('foreign_id')->nullable()->comment('any model that need to have multi image');
            $table->string('foreign_model')->nullable()->comment('namespace of model itself');
            $table->string('image', 255)->nullable();
            $table->json('title')->nullable()->comment('multi language');
            $table->json('description')->nullable()->comment('multi language');
            $table->bigInteger('user_id')->nullable()->comment('last performed');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('galleries');
    }
};
