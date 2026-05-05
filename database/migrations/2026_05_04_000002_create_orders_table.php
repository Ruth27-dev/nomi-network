<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('orders')) {
            return;
        }

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_no', 60)->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('user_address_id')->nullable()->constrained('user_addresses')->nullOnDelete();
            $table->unsignedBigInteger('shipping_method_id')->nullable();
            $table->string('shipping_method_title', 255)->nullable();
            $table->string('recipient_name', 255)->nullable();
            $table->string('recipient_phone', 50)->nullable();
            $table->text('shipping_address')->nullable();
            $table->text('note')->nullable();
            $table->decimal('sub_total', 14, 2)->default(0);
            $table->decimal('shipping_fee', 14, 2)->default(0);
            $table->decimal('discount_amount', 14, 2)->default(0);
            $table->decimal('grand_total', 14, 2)->default(0);
            $table->string('payment_method', 50)->default('cod');
            $table->string('payment_status', 50)->default('unpaid');
            $table->string('status', 50)->default('pending');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

