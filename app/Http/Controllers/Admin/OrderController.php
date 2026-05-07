<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ProductStock;
use Exception;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:order-view', ['only' => ['index', 'data', 'detail']]);
        $this->middleware('permission:order-update', ['only' => ['updateStatus']]);
    }

    public function index()
    {
        return view('admin::pages.order.index');
    }

    public function data()
    {
        try {
            $pag = request('pag') ?? 50;
            $data = Order::query()
                ->withCount('items')
                ->with('user:id,name,phone')
                ->when(request('status'), fn($q) => $q->where('status', request('status')))
                ->when(request('search'), function ($q) {
                    $search = request('search');
                    $q->where(function ($sub) use ($search) {
                        $sub->where('order_no', 'LIKE', "%{$search}%")
                            ->orWhere('recipient_name', 'LIKE', "%{$search}%")
                            ->orWhere('recipient_phone', 'LIKE', "%{$search}%");
                    });
                })
                ->orderByDesc('id')
                ->paginate($pag);

            return $data;
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }
    }

    public function detail()
    {
        try {
            $data = Order::query()
                ->with(['user:id,name,phone', 'items'])
                ->findOrFail(request('id'));

            $stocks = collect();
            $stockHistories = collect();
            if ($data->items->isNotEmpty()) {
                $stocks = ProductStock::query()
                    ->where(function ($q) use ($data) {
                        foreach ($data->items as $item) {
                            $q->orWhere(function ($sub) use ($item) {
                                $sub->where('product_id', $item->product_id);
                                if ($item->product_variation_id) {
                                    $sub->where('product_variation_id', $item->product_variation_id);
                                } else {
                                    $sub->whereNull('product_variation_id');
                                }
                            });
                        }
                    })
                    ->get(['product_id', 'product_variation_id', 'stock_on_hand', 'stock_reserved', 'stock_available']);

                $stockHistories = DB::table('stock_history as sh')
                    ->selectRaw('sh.product_id, sh.product_variation_id, MAX(sh.created_at) as latest_stock_history_at')
                    ->where(function ($q) use ($data) {
                        foreach ($data->items as $item) {
                            $q->orWhere(function ($sub) use ($item) {
                                $sub->where('sh.product_id', $item->product_id);
                                if ($item->product_variation_id) {
                                    $sub->where('sh.product_variation_id', $item->product_variation_id);
                                } else {
                                    $sub->whereNull('sh.product_variation_id');
                                }
                            });
                        }
                    })
                    ->groupBy('sh.product_id', 'sh.product_variation_id')
                    ->get();
            }

            $stockByKey = $stocks->keyBy(function ($row) {
                return $row->product_id . ':' . ($row->product_variation_id ?? 'null');
            });
            $historyByKey = $stockHistories->keyBy(function ($row) {
                return $row->product_id . ':' . ($row->product_variation_id ?? 'null');
            });

            $data->items->transform(function ($item) use ($stockByKey, $historyByKey) {
                $key = $item->product_id . ':' . ($item->product_variation_id ?? 'null');
                $stock = $stockByKey->get($key);
                $history = $historyByKey->get($key);

                $item->current_stock = [
                    'stock_on_hand' => (int) ($stock->stock_on_hand ?? 0),
                    'stock_reserved' => (int) ($stock->stock_reserved ?? 0),
                    'stock_available' => (int) ($stock->stock_available ?? 0),
                ];
                $item->latest_stock_history_at = $history->latest_stock_history_at ?? null;

                return $item;
            });

            return $this->responseSuccess($data);
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }
    }

    public function updateStatus()
    {
        DB::beginTransaction();
        try {
            $order = Order::query()->with('items')->findOrFail(request('id'));
            $newStatus = strtolower((string) request('status'));

            if (!in_array($newStatus, ['pending', 'confirmed', 'shipping', 'completed', 'cancelled'], true)) {
                return $this->responseError('Invalid order status.');
            }

            if ($order->status === $newStatus) {
                DB::commit();
                return $this->responseSuccess(null, 'No status change.');
            }

            if ($newStatus === 'cancelled' && in_array($order->status, ['pending', 'confirmed'], true)) {
                $this->releaseReservedStock($order);
            }

            if ($newStatus === 'completed' && $order->status !== 'completed') {
                // Release reserved first; stock-on-hand deduction happens in DB trigger.
                $this->releaseReservedStock($order);
                $order->completed_at = now();
            }

            $order->status = $newStatus;
            $order->save();

            DB::commit();
            return $this->responseSuccess(null, 'Order status updated successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return $this->responseError($e->getMessage());
        }
    }

    private function releaseReservedStock(Order $order): void
    {
        foreach ($order->items as $item) {
            $stock = ProductStock::query()
                ->where('product_id', $item->product_id)
                ->where(function ($q) use ($item) {
                    if ($item->product_variation_id) {
                        $q->where('product_variation_id', $item->product_variation_id);
                    } else {
                        $q->whereNull('product_variation_id');
                    }
                })
                ->lockForUpdate()
                ->first();

            if (!$stock) {
                continue;
            }

            $stock->stock_reserved = max(0, (int) $stock->stock_reserved - (int) $item->quantity);
            $stock->stock_available = (int) $stock->stock_on_hand - (int) $stock->stock_reserved;
            $stock->save();
        }
    }
}
