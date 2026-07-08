<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        // MySQL uses trigger body directly; no standalone trigger function.
        if ($driver === 'mysql') {
            return;
        }

        if ($driver !== 'pgsql') {
            return;
        }

        if (!Schema::hasTable('orders') || !Schema::hasTable('order_items')) {
            return;
        }

        DB::unprepared(<<<'SQL'
CREATE OR REPLACE FUNCTION update_stock_on_order_complete()
RETURNS trigger AS $$
BEGIN
  UPDATE product_stocks
  SET stock_on_hand = stock_on_hand - oi.quantity,
      stock_available = stock_on_hand - stock_reserved - oi.quantity,
      updated_at = NOW()
  FROM order_items oi
  WHERE oi.order_id = NEW.id
    AND product_stocks.product_id = oi.product_id
    AND (
        product_stocks.product_variation_id = oi.product_variation_id
        OR (product_stocks.product_variation_id IS NULL AND oi.product_variation_id IS NULL)
    );

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

  RETURN NEW;
END;
$$ LANGUAGE plpgsql;
SQL);
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            DB::unprepared('DROP FUNCTION IF EXISTS update_stock_on_order_complete();');
        }
    }
};
