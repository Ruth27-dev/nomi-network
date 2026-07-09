<?php

use App\Models\Product;
use App\Models\ProductStock;
use App\Models\ProductVariation;
use App\Services\StockService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * Admin product/variation edits never wrote through to product_stocks, so
 * stock_on_hand there could go stale after any edit while products.stock /
 * product_variations.stock (shown in admin) kept updating. This backfills
 * product_stocks from those source-of-truth columns, one time, recomputing
 * stock_reserved from genuinely-pending PayWay transactions the same way
 * ProductStockController::adjust() already does.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('product_stocks') || !Schema::hasTable('products')) {
            return;
        }

        $stock = app(StockService::class);

        Product::query()->select(['id', 'stock'])->chunkById(200, function ($products) use ($stock) {
            foreach ($products as $product) {
                $onHand = (int) ($product->stock ?? 0);
                $trueReserved = $stock->computeActiveReserved($product->id, null);

                ProductStock::updateOrCreate(
                    ['product_id' => $product->id, 'product_variation_id' => null],
                    [
                        'stock_on_hand'   => $onHand,
                        'stock_reserved'  => $trueReserved,
                        'stock_available' => max(0, $onHand - $trueReserved),
                    ]
                );
            }
        });

        if (!Schema::hasTable('product_variations')) {
            return;
        }

        ProductVariation::query()->select(['id', 'product_id', 'stock'])->chunkById(200, function ($variations) use ($stock) {
            foreach ($variations as $variation) {
                $onHand = (int) ($variation->stock ?? 0);
                $trueReserved = $stock->computeActiveReserved($variation->product_id, $variation->id);

                ProductStock::updateOrCreate(
                    ['product_id' => $variation->product_id, 'product_variation_id' => $variation->id],
                    [
                        'stock_on_hand'   => $onHand,
                        'stock_reserved'  => $trueReserved,
                        'stock_available' => max(0, $onHand - $trueReserved),
                    ]
                );
            }
        });
    }

    public function down(): void
    {
        // Data backfill only; not reversible.
    }
};
