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
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('page')->nullable();
            $table->json('title')->nullable();
            $table->json('short_detail')->nullable();
            $table->json('content')->nullable();
            $table->integer('ordering')->nullable();
            $table->string('image')->nullable();
            $table->string('status')->default(config('dummy.status.active.key'));
            $table->tinyInteger('user_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
