<?php

namespace App\Services;

use App\Models\Order;
use App\Models\ProductStock;
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
        $alreadyDeducted = DB::table('stock_history')
            ->where('order_id', $order->id)
            ->where('transaction_type', 'sale')
            ->exists();

        if ($alreadyDeducted) {
            return;
        }

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
            $after  = max(0, $before - $qty);

            $stock->stock_on_hand   = $after;
            $stock->stock_reserved  = max(0, (int) $stock->stock_reserved - $qty);
            $stock->stock_available = max(0, $stock->stock_on_hand - $stock->stock_reserved);
            $stock->save();

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
}
