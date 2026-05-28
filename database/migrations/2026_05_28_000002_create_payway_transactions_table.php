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
        Schema::create('payway_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->foreignId('donation_id')->nullable()->constrained('donations')->nullOnDelete();
            $table->string('tran_id')->unique();
            $table->unsignedTinyInteger('is_update')->nullable();
            $table->string('tran_type', 50)->nullable();
            $table->string('order_type', 30)->nullable();
            $table->string('status_code', 10)->nullable();
            $table->string('payment_status', 50)->nullable();
            $table->json('raw_callback')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payway_transactions');
    }
};

