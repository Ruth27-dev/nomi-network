<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductStock;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class ProductStockController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:product-stock-view', ['only' => ['index', 'data', 'history']]);
        $this->middleware('permission:product-stock-update', ['only' => ['adjust']]);
    }

    public function index()
    {
        return view('admin::pages.product-stock.index');
    }

    public function data()
    {
        try {
            $pag = request('pag') ?? 50;
            $data = ProductStock::query()
                ->with(['product:id,sku,name_en,name_kh', 'variation:id,sku,name'])
                ->when(request('search'), function ($q) {
                    $search = request('search');
                    $q->whereHas('product', function ($sub) use ($search) {
                        $sub->where('sku', 'LIKE', "%{$search}%")
                            ->orWhere('name_en', 'LIKE', "%{$search}%")
                            ->orWhere('name_kh', 'LIKE', "%{$search}%");
                    })->orWhereHas('variation', function ($sub) use ($search) {
                        $sub->where('sku', 'LIKE', "%{$search}%")
                            ->orWhere('name', 'LIKE', "%{$search}%");
                    });
                })
                ->orderByDesc('updated_at')
                ->paginate($pag);

            return $data;
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }
    }

    public function history()
    {
        try {
            $pag = request('pag') ?? 50;
            $query = DB::table('stock_history as sh')
                ->leftJoin('products as p', 'p.id', '=', 'sh.product_id')
                ->leftJoin('product_variations as pv', 'pv.id', '=', 'sh.product_variation_id')
                ->select(
                    'sh.id',
                    'sh.order_id',
                    'sh.product_id',
                    'sh.product_variation_id',
                    'sh.quantity',
                    'sh.transaction_type',
                    'sh.stock_before',
                    'sh.stock_after',
                    'sh.created_at',
                    'p.sku as product_sku',
                    'p.name_en as product_name_en',
                    'pv.sku as variation_sku',
                    'pv.name as variation_name',
                )
                ->when(request('product_id'), fn($q) => $q->where('sh.product_id', request('product_id')))
                ->when(request('transaction_type'), fn($q) => $q->where('sh.transaction_type', request('transaction_type')))
                ->orderByDesc('sh.id');

            return $query->paginate($pag);
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }
    }

    public function adjust(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:product_stocks,id',
                'adjust_qty' => 'required|integer|not_in:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => __('validate.attributes.required'),
                    'errors' => $validator->errors(),
                ], 422);
            }

            $stock = ProductStock::findOrFail($request->id);
            $before = (int) $stock->stock_on_hand;
            $adjust = (int) $request->adjust_qty;
            $after = $before + $adjust;

            if ($after < 0) {
                DB::rollBack();
                return $this->responseError('Stock on hand cannot be negative.');
            }

            $reserved = (int) $stock->stock_reserved;
            $available = max(0, $after - $reserved);

            $stock->update([
                'stock_on_hand' => $after,
                'stock_available' => $available,
            ]);

            if (Schema::hasTable('stock_history')) {
                $payload = [
                    'order_id' => 0,
                    'product_id' => $stock->product_id,
                    'product_variation_id' => $stock->product_variation_id,
                    'quantity' => abs($adjust),
                    'transaction_type' => 'adjustment',
                    'stock_before' => $before,
                    'stock_after' => $after,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                try {
                    DB::table('stock_history')->insert($payload);
                } catch (\Throwable $e) {
                    // Keep adjustment successful even if history insert is blocked by strict FK.
                }
            }

            DB::commit();
            return $this->responseSuccess(null, 'Stock adjusted successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return $this->responseError($e->getMessage());
        }
    }
}

