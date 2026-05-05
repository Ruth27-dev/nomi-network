<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('stock_history')) {
            return;
        }

        Schema::create('stock_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('product_variation_id')->nullable()->constrained('product_variations')->nullOnDelete();
            $table->integer('quantity');
            $table->text('transaction_type');
            $table->integer('stock_before');
            $table->integer('stock_after');
            $table->timestamps();
        });

        try {
            DB::statement("ALTER TABLE stock_history ADD CONSTRAINT stock_history_transaction_type_check CHECK (transaction_type IN ('sale', 'return', 'adjustment'))");
        } catch (\Throwable $e) {
            // Ignore if constraint already exists.
        }

        if (Schema::hasTable('orders')) {
            try {
                DB::statement('ALTER TABLE stock_history ADD CONSTRAINT stock_history_order_id_fkey FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE');
            } catch (\Throwable $e) {
                // Ignore if FK already exists.
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_history');
    }
};
