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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('branch_id')->nullable();
            $table->string('code', 255)->nullable();
            $table->json('title')->nullable()->comment('multi language');
            $table->boolean('is_sellable')->nullable()->default(false)->comment('This item (or one of its variates) can be sold to customers.');
            $table->boolean('is_consumable')->nullable()->default(true)->comment('This item is used in recipes, i.e., stock is consumed to make other products.');
            $table->boolean('is_vat')->nullable()->default(false);
            $table->boolean('is_popular')->nullable()->default(false);
            $table->string('status')->nullable()->comment('ACTIVE, INACTIVE');
            $table->string('type')->nullable()->comment('CUSTOMER, POS');
            $table->json('description')->nullable()->comment('multi language');
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
        Schema::dropIfExists('products');
    }
};
