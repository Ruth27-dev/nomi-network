<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('orders') || !Schema::hasTable('order_items')) {
            return;
        }

        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::unprepared('DROP TRIGGER IF EXISTS trigger_stock_update_on_order_complete');
            DB::unprepared(<<<'SQL'
CREATE TRIGGER trigger_stock_update_on_order_complete
AFTER UPDATE ON orders
FOR EACH ROW
BEGIN
    IF NEW.status = 'completed' AND (OLD.status IS NULL OR OLD.status <> 'completed') THEN
        UPDATE product_stocks ps
        JOIN order_items oi
            ON ps.product_id = oi.product_id
           AND (
                ps.product_variation_id = oi.product_variation_id
                OR (ps.product_variation_id IS NULL AND oi.product_variation_id IS NULL)
           )
        SET
            ps.stock_on_hand = ps.stock_on_hand - oi.quantity,
            ps.stock_available = ps.stock_on_hand - ps.stock_reserved - oi.quantity,
            ps.updated_at = NOW()
        WHERE oi.order_id = NEW.id;

        INSERT INTO stock_history (
            order_id,
            product_id,
            product_variation_id,
            quantity,
            transaction_type,
            stock_before,
            stock_after,
            created_at,
            updated_at
        )
        SELECT
            oi.order_id,
            oi.product_id,
            oi.product_variation_id,
            oi.quantity,
            'sale',
            ps.stock_on_hand + oi.quantity,
            ps.stock_on_hand,
            NOW(),
            NOW()
        FROM order_items oi
        JOIN product_stocks ps
          ON ps.product_id = oi.product_id
         AND (
              ps.product_variation_id = oi.product_variation_id
              OR (ps.product_variation_id IS NULL AND oi.product_variation_id IS NULL)
         )
        WHERE oi.order_id = NEW.id;
    END IF;
END
SQL);
            return;
        }

        if ($driver !== 'pgsql') {
            return;
        }

        DB::unprepared(<<<'SQL'
CREATE TRIGGER trigger_stock_update_on_order_complete
AFTER UPDATE OF status
ON orders
FOR EACH ROW
WHEN (NEW.status = 'completed' AND OLD.status IS DISTINCT FROM 'completed')
EXECUTE FUNCTION update_stock_on_order_complete();
SQL);
    }

    public function down(): void
    {
        if (Schema::hasTable('orders')) {
            if (DB::getDriverName() === 'mysql') {
                DB::unprepared('DROP TRIGGER IF EXISTS trigger_stock_update_on_order_complete');
                return;
            }
            DB::unprepared('DROP TRIGGER IF EXISTS trigger_stock_update_on_order_complete ON public.orders;');
        }
    }
};
