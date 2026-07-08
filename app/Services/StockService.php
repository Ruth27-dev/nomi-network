<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\ProductVariation;
use Illuminate\Support\Facades\DB;

class StockService
{
    /**
     * Deduct stock_on_hand for every item in a confirmed order and record
     * a 'sale' history entry per item.
     *
     * Idempotent — safe to call multiple times for the same order; the
     * existing stock_history rows act as the guard against double-deduction.
     */
    public function deductForOrder(Order $order): void
    {
        $items = ($order->relationLoaded('items') ? $order->items : $order->load('items')->items)
            ->groupBy(fn ($item) => $item->product_id . ':' . ($item->product_variation_id ?? 'null'))
            ->map(function ($items) {
                $first = $items->first();
                return (object) [
                    'product_id' => $first->product_id,
                    'product_variation_id' => $first->product_variation_id,
                    'quantity' => $items->sum(fn ($item) => max(0, (int) $item->quantity)),
                ];
            })
            ->filter(fn ($item) => $item->quantity > 0);

        foreach ($items as $item) {
            $alreadyDeducted = DB::table('stock_history')
                ->where('order_id', $order->id)
                ->where('product_id', $item->product_id)
                ->where('transaction_type', 'sale')
                ->where(function ($q) use ($item) {
                    $item->product_variation_id
                        ? $q->where('product_variation_id', $item->product_variation_id)
                        : $q->whereNull('product_variation_id');
                })
                ->exists();

            if ($alreadyDeducted) {
                continue;
            }

            $stock = ProductStock::query()
                ->where('product_id', $item->product_id)
                ->where(function ($q) use ($item) {
                    $item->product_variation_id
                        ? $q->where('product_variation_id', $item->product_variation_id)
                        : $q->whereNull('product_variation_id');
                })
                ->lockForUpdate()
                ->first();

            if (!$stock) {
                continue;
            }

            $qty    = (int) $item->quantity;
            $before = (int) $stock->stock_on_hand;
            $after  = max(0, $before - $qty);

            $stock->stock_on_hand   = $after;
            $stock->stock_reserved  = max(0, (int) $stock->stock_reserved - $qty);
            $stock->stock_available = max(0, $stock->stock_on_hand - $stock->stock_reserved);
            $stock->save();

            $this->syncProductStock($item->product_id, $item->product_variation_id, $after);

            DB::table('stock_history')->insert([
                'order_id'             => $order->id,
                'product_id'           => $item->product_id,
                'product_variation_id' => $item->product_variation_id,
                'quantity'             => $qty,
                'transaction_type'     => 'sale',
                'stock_before'         => $before,
                'stock_after'          => $after,
                'created_at'           => now(),
                'updated_at'           => now(),
            ]);
        }
    }

    /**
     * Return stock_on_hand when a confirmed order is cancelled.
     * Records a 'return' history entry per item.
     */
    public function returnForOrder(Order $order): void
    {
        $items = $order->relationLoaded('items') ? $order->items : $order->load('items')->items;

        foreach ($items as $item) {
            $stock = ProductStock::query()
                ->where('product_id', $item->product_id)
                ->where(function ($q) use ($item) {
                    $item->product_variation_id
                        ? $q->where('product_variation_id', $item->product_variation_id)
                        : $q->whereNull('product_variation_id');
                })
                ->lockForUpdate()
                ->first();

            if (!$stock) {
                continue;
            }

            $qty    = (int) $item->quantity;
            $before = (int) $stock->stock_on_hand;
            $after  = $before + $qty;

            $stock->stock_on_hand   = $after;
            $stock->stock_available = max(0, $stock->stock_on_hand - (int) $stock->stock_reserved);
            $stock->save();

            $this->syncProductStock($item->product_id, $item->product_variation_id, $after);

            DB::table('stock_history')->insert([
                'order_id'             => $order->id,
                'product_id'           => $item->product_id,
                'product_variation_id' => $item->product_variation_id,
                'quantity'             => $qty,
                'transaction_type'     => 'return',
                'stock_before'         => $before,
                'stock_after'          => $after,
                'created_at'           => now(),
                'updated_at'           => now(),
            ]);
        }
    }

    /**
     * Sum the qty currently reserved by PayWay transactions that are
     * genuinely still pending: no order created yet, unpaid, and within
     * the 30-minute checkout window (older ones are abandoned/expired).
     */
    public function computeActiveReserved(int $productId, ?int $variationId): int
    {
        $rows = DB::table('payway_transactions')
            ->whereNull('order_id')
            ->where('payment_status', 'unpaid')
            ->where('created_at', '>=', now()->subMinutes(30))
            ->whereNotNull('raw_callback')
            ->pluck('raw_callback');

        $total = 0;
        foreach ($rows as $raw) {
            $data  = is_string($raw) ? json_decode($raw, true) : $raw;
            $items = $data['pending_order']['items'] ?? [];
            foreach ($items as $item) {
                $itemProductId   = (int) ($item['product_id'] ?? 0);
                $itemVariationId = isset($item['product_variation_id']) ? (int) $item['product_variation_id'] : null;

                if ($itemProductId !== $productId) {
                    continue;
                }
                if ($variationId === null && $itemVariationId !== null) {
                    continue;
                }
                if ($variationId !== null && $itemVariationId !== $variationId) {
                    continue;
                }

                $total += (int) ($item['quantity'] ?? 0);
            }
        }

        return $total;
    }

    private function syncProductStock(int $productId, ?int $variationId, int $onHand): void
    {
        if ($variationId) {
            ProductVariation::where('id', $variationId)->update(['stock' => $onHand]);
        } else {
            Product::where('id', $productId)->update(['stock' => $onHand]);
        }
    }
}
