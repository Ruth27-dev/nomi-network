<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('donations')) {
            return;
        }

        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->string('tran_id')->unique();                 // PayWay transaction ID (DON-...)
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('donation_type')->default('one_time'); // one_time | monthly
            $table->decimal('amount', 14, 2);
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('email')->nullable();
            $table->string('payment_option')->nullable();         // abapay_khqr | cards | abapay_khqr_deeplink
            $table->string('payment_status')->default('unpaid'); // unpaid | pending | paid | failed
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
