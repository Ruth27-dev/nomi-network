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
        Schema::create('list_of_values', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('branch_id')->nullable();
            $table->string('code', 255)->nullable();
            $table->string('type')->nullable()->comment('gender, branch, etc...');
            $table->json('title')->nullable()->comment('multi language');
            $table->json('description')->nullable()->comment('multi language');
            $table->string('status')->nullable()->comment('ACTIVE, INACTIVE');
            $table->json('add_on')->nullable()->comment('if need more data can store json here');
            $table->bigInteger('sequence')->nullable()->comment('ordering');
            $table->string('image', 255)->nullable();
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
        Schema::dropIfExists('list_of_values');
    }
};
