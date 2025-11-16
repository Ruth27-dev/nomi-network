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
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->longText('name');
            $table->integer('parent_id')->nullable();
            $table->string('icon')->nullable();
            $table->text('path')->nullable();
            $table->text('active')->nullable();
            $table->integer('ordering')->nullable();
            $table->text('permission')->nullable();
            $table->timestamp('disabled_at')->nullable();
            $table->string('key')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
