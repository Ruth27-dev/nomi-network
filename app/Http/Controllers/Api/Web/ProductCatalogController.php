<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Exception;
use Illuminate\Support\Facades\DB;

class ProductCatalogController extends Controller
{
    public function categories()
    {
        try {
            $data = Category::query()
                ->where('status', $this->active)
                ->orderBy('id')
                ->get()
                ->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'parent_id' => $category->parent_id,
                        'slug' => $category->slug,
                        'title' => $category->title,
                        'description' => $category->description,
                        'status' => $category->status,
                    ];
                })
                ->values();

            return $this->responseSuccess($data);
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }
    }

    public function products()
    {
        try {
            $query = Product::query()
                ->with([
                    'category:id,parent_id,title,slug,status',
                    'images:id,foreign_id,foreign_model,image',
                    'productVariations:id,product_id,sku,name,price,stock,is_active',
                ])
                ->where('is_active', true)
                ->when(request('category_id'), function ($q) {
                    $ids = Category::descendantIds((int) request('category_id'));
                    $q->whereIn('category_id', $ids);
                })
                ->when(request()->has('is_feature') || request()->has('is_fature'), function ($q) {
                    $isFeature = request()->has('is_feature') ? request('is_feature') : request('is_fature');
                    $isFeature = filter_var($isFeature, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                    if ($isFeature !== null) {
                        $q->where('is_feature', $isFeature);
                    }
                })
                ->when(request('search'), function ($q) {
                    $search = trim((string) request('search'));
                    $q->where(function ($inner) use ($search) {
                        $inner->where('sku', 'like', "%{$search}%")
                            ->orWhere('name_en', 'like', "%{$search}%")
                            ->orWhere('name_kh', 'like', "%{$search}%");
                    });
                });

            if (request('id')) {
                $product = $query->where('id', request('id'))->first();
                return $this->responseSuccess($product);
            }

            $perPage = (int) request('per_page', 12);
            $perPage = $perPage > 0 ? min($perPage, 100) : 12;

            $products = $query->orderByDesc('id')->paginate($perPage);

            return $this->responseSuccess([
                'data' => $products->items(),
                'paginate' => [
                    'total' => $products->total(),
                    'count' => $products->count(),
                    'per_page' => $products->perPage(),
                    'current_page' => $products->currentPage(),
                    'total_pages' => $products->lastPage(),
                    'next_page_url' => $products->nextPageUrl(),
                    'prev_page_url' => $products->previousPageUrl(),
                ],
            ]);
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }
    }

    public function productDetail()
    {
        try {
            $id = request('id');
            if (!$id) {
                return $this->responseError('Product id is required.');
            }

            $product = Product::query()
                ->with([
                    'category:id,parent_id,title,slug,status',
                    'images:id,foreign_id,foreign_model,image',
                    'productVariations:id,product_id,sku,name,price,stock,is_active',
                ])
                ->where('is_active', true)
                ->where('id', $id)
                ->first();

            if (!$product) {
                return $this->responseSuccess(null, 'Data not found');
            }

            return $this->responseSuccess($product);
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }
    }

    public function categoryTree()
    {
        try {
            $categories = Category::query()
                ->where('status', $this->active)
                ->orderBy('id')
                ->get(['id', 'parent_id', 'title', 'description', 'slug', 'status']);

            $byParent = $categories->groupBy('parent_id');

            $makeTree = function ($parentId) use (&$makeTree, $byParent) {
                return ($byParent->get($parentId) ?? collect())->map(function ($item) use (&$makeTree) {
                    return [
                        'id' => $item->id,
                        'parent_id' => $item->parent_id,
                        'slug' => $item->slug,
                        'title' => $item->title,
                        'description' => $item->description,
                        'status' => $item->status,
                        'children' => $makeTree($item->id)->values(),
                    ];
                })->values();
            };

            return $this->responseSuccess($makeTree(null)->values());
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }
    }

    public function stockSummary()
    {
        try {
            $query = DB::table('product_stocks as ps')
                ->leftJoin('products as p', 'p.id', '=', 'ps.product_id')
                ->leftJoin('product_variations as pv', 'pv.id', '=', 'ps.product_variation_id')
                ->where('p.is_active', 1)
                ->when(request('product_id'), fn($q) => $q->where('ps.product_id', request('product_id')))
                ->when(request('product_variation_id'), fn($q) => $q->where('ps.product_variation_id', request('product_variation_id')))
                ->select([
                    'ps.id',
                    'ps.product_id',
                    'ps.product_variation_id',
                    'ps.stock_on_hand',
                    'ps.stock_reserved',
                    'ps.stock_available',
                    'ps.updated_at',
                    'p.sku as product_sku',
                    'p.name_en as product_name_en',
                    'p.name_kh as product_name_kh',
                    'pv.sku as variation_sku',
                    'pv.name as variation_name',
                ])
                ->orderByDesc('ps.updated_at');

            $perPage = (int) request('per_page', 20);
            $perPage = $perPage > 0 ? min($perPage, 100) : 20;
            $stocks = $query->paginate($perPage);

            return $this->responseSuccess([
                'data' => $stocks->items(),
                'paginate' => [
                    'total' => $stocks->total(),
                    'count' => $stocks->count(),
                    'per_page' => $stocks->perPage(),
                    'current_page' => $stocks->currentPage(),
                    'total_pages' => $stocks->lastPage(),
                    'next_page_url' => $stocks->nextPageUrl(),
                    'prev_page_url' => $stocks->previousPageUrl(),
                ],
            ]);
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }
    }

    public function stockHistory()
    {
        try {
            $query = DB::table('stock_history as sh')
                ->leftJoin('products as p', 'p.id', '=', 'sh.product_id')
                ->leftJoin('product_variations as pv', 'pv.id', '=', 'sh.product_variation_id')
                ->when(request('product_id'), fn($q) => $q->where('sh.product_id', request('product_id')))
                ->when(request('order_id'), fn($q) => $q->where('sh.order_id', request('order_id')))
                ->when(request('transaction_type'), fn($q) => $q->where('sh.transaction_type', request('transaction_type')))
                ->select([
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
                    'p.name_kh as product_name_kh',
                    'pv.sku as variation_sku',
                    'pv.name as variation_name',
                ])
                ->orderByDesc('sh.id');

            $perPage = (int) request('per_page', 20);
            $perPage = $perPage > 0 ? min($perPage, 100) : 20;
            $history = $query->paginate($perPage);

            return $this->responseSuccess([
                'data' => $history->items(),
                'paginate' => [
                    'total' => $history->total(),
                    'count' => $history->count(),
                    'per_page' => $history->perPage(),
                    'current_page' => $history->currentPage(),
                    'total_pages' => $history->lastPage(),
                    'next_page_url' => $history->nextPageUrl(),
                    'prev_page_url' => $history->previousPageUrl(),
                ],
            ]);
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }
    }
}
