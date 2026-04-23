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
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['COUPON', 'DISCOUNT'])->nullable();
            $table->json('title')->nullable();
            $table->string('code')->nullable()->comment('coupon');
            $table->double('discount_amount')->nullable()->comment('discount amount');
            $table->enum('discount_type', ['AMOUNT', 'PERCENTAGE'])->default('AMOUNT');
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->integer('usage_limit')->nullable();
            $table->integer('usage_per_customer')->nullable();
            $table->boolean('is_flat_discount')->default(false)->nullable();
            $table->json('remark')->nullable();
            $table->string('status')->nullable()->comment('ACTIVE, INACTIVE');
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
        Schema::dropIfExists('discounts');
    }
};
