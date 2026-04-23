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
        Schema::create('social_media', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('branch_id')->nullable();
            $table->json('title', 255)->nullable();
            $table->string('image')->nullable();
            $table->integer('ordering')->nullable();
            $table->text('url')->nullable();
            $table->string('status')->nullable()->comment('active, inactive');
            $table->bigInteger('user_id')->nullable()->comment('last performed');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_media');
    }
};
