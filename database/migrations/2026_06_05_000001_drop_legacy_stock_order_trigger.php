<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('orders')) {
            return;
        }

        if (DB::getDriverName() === 'mysql') {
            DB::unprepared('DROP TRIGGER IF EXISTS trigger_stock_update_on_order_complete');
            return;
        }

        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::unprepared('DROP TRIGGER IF EXISTS trigger_stock_update_on_order_complete ON public.orders;');
        DB::unprepared('DROP FUNCTION IF EXISTS update_stock_on_order_complete();');
    }

    public function down(): void
    {
        // Stock deduction is owned by App\Services\StockService. The old
        // database trigger duplicated that behavior and is intentionally not
        // recreated on rollback.
    }
};
