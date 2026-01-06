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
        Schema::create('order_discount_usages', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('discount_id');
            $table->bigInteger('customer_id')->nullable();
            $table->bigInteger('order_id');
            $table->bigInteger('order_detail_id')->nullable();
            $table->json('discount_data')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_discount_usages');
    }
};
