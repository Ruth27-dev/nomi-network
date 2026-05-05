<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductVariationRequest;
use App\Models\Product;
use App\Models\ProductVariation;
use Exception;
use Illuminate\Support\Facades\DB;

class ProductVariationController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:product-variation-view', ['only' => ['index', 'detail', 'data']]);
        $this->middleware('permission:product-variation-create', ['only' => ['save']]);
        $this->middleware('permission:product-variation-update', ['only' => ['save', 'updateStatus']]);
        $this->middleware('permission:product-variation-delete', ['only' => ['delete']]);
    }

    public function index()
    {
        $products = Product::query()
            ->where('is_active', true)
            ->orderBy('id', 'desc')
            ->get();

        return view('admin::pages.product-variation.index', [
            'products' => $products,
        ]);
    }

    public function data()
    {
        try {
            $pag = request('pag') ?? 50;
            $data = ProductVariation::query()
                ->with('product')
                ->when(request('status'), fn($q) => $q->where('is_active', request('status') === $this->active))
                ->when(request('product_id'), fn($q) => $q->where('product_id', request('product_id')))
                ->when(request('search'), function ($q) {
                    $q->where(function ($query) {
                        $query->where('name', 'LIKE', '%' . request('search') . '%');
                        $query->orWhere('sku', 'LIKE', '%' . request('search') . '%');
                        $query->orWhere('barcode', 'LIKE', '%' . request('search') . '%');
                        $query->orWhereHas('product', function ($product) {
                            $product->where('name_en', 'LIKE', '%' . request('search') . '%');
                            $product->orWhere('name_kh', 'LIKE', '%' . request('search') . '%');
                        });
                    });
                })
                ->orderByDesc('created_at')
                ->paginate($pag);
            return $data;
        } catch (Exception $e) {
            return $this->responseError();
        }
    }

    public function detail()
    {
        try {
            $data = ProductVariation::with(['product', 'images'])->findOrFail(request('id'));
            return $this->responseSuccess($data);
        } catch (Exception $e) {
            return $this->responseError();
        }
    }

    public function save(ProductVariationRequest $request)
    {
        DB::beginTransaction();
        try {
            $payload = [
                'product_id' => $request->product_id,
                'sku' => $request->sku,
                'barcode' => $request->barcode,
                'name' => $request->title_en,
                'combination_key' => $request->combination_key,
                'price' => $request->price,
                'stock' => $request->stock ?? 0,
                'is_active' => $request->status === $this->active,
            ];

            if (!$request->id) {
                $variation = ProductVariation::create($payload);
            } else {
                $variation = ProductVariation::findOrFail($request->id);
                $variation->update($payload);
            }

            DB::commit();
            return $this->responseSuccess();
        } catch (Exception $e) {
            DB::rollBack();
            return $this->responseError();
        }
    }

    public function updateStatus()
    {
        DB::beginTransaction();
        try {
            $data = ProductVariation::findOrFail(request('id'));
            $data->update([
                'is_active' => request('status') === $this->active,
            ]);
            DB::commit();
            return $this->responseSuccess(null, __('form.message.update.success'));
        } catch (Exception $e) {
            DB::rollBack();
            return $this->responseError();
        }
    }

    public function delete()
    {
        DB::beginTransaction();
        try {
            ProductVariation::findOrFail(request('id'))->delete();
            DB::commit();
            return $this->responseSuccess(null, __('form.message.delete.success'));
        } catch (Exception $e) {
            DB::rollBack();
            return $this->responseError();
        }
    }
}
