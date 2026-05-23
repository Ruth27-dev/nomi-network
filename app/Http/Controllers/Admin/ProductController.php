<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Category;
use App\Models\Gallery;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\UploadFile;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:product-view', ['only' => ['index', 'detail', 'data']]);
        $this->middleware('permission:product-create', ['only' => ['save']]);
        $this->middleware('permission:product-update', ['only' => ['save']]);
        $this->middleware('permission:product-delete', ['only' => ['delete']]);
        $this->middleware('permission:product-restore', ['only' => ['restore']]);
    }

    public function index()
    {
        $data = [
            'categories' => Category::query()->active()->get(),
            'type'      => request('type'),
        ];

        return view('admin::pages.product.index', $data);
    }

    public function data()
    {
        try {
            $pag = request('pag') ?? 50;
            $data = Product::query()
                ->with(['category', 'images'])
                ->when(request('status'), function ($q) {
                    $q->where('is_active', request('status') === $this->active);
                })
                ->when(request('trash'), fn($q) => $q->onlyTrashed())
                ->when(request('category_id'), function ($q) {
                    $ids = Category::descendantIds((int) request('category_id'));
                    $q->whereHas('categories', fn($q) => $q->whereIn('categories.id', $ids));
                })
                ->when(request('search'), function ($q) {
                    $q->where(function ($q) {
                        $q->where('sku', 'LIKE', '%' . request('search') . '%');
                        $q->orWhere('name_en', 'LIKE', '%' . request('search') . '%');
                        $q->orWhere('name_kh', 'LIKE', '%' . request('search') . '%');
                        $q->orWhere('description_en', 'LIKE', '%' . request('search') . '%');
                        $q->orWhere('description_kh', 'LIKE', '%' . request('search') . '%');
                    });
                })
                ->orderByDesc('created_at')
                ->paginate($pag);
            return $data;
        } catch (Exception $e) {
            return $this->responseError();
        }
    }

    public function save(ProductRequest $request)
    {
        DB::beginTransaction();

        try {

            /* ================= PRODUCT ================= */

            // Collect all category IDs (support both category_ids[] array and single category_id)
            $categoryIds = collect(is_array($request->category_ids) ? $request->category_ids : [$request->category_id])
                ->filter()
                ->map(fn($id) => (int) $id)
                ->unique()
                ->values()
                ->all();

            // Keep category_id (first one) for backward compatibility
            $primaryCategoryId = $categoryIds[0] ?? null;

            if (!$request->id) {

                /* ================= CREATE ================= */

                $product = Product::create([
                    'sku' => $request->code,
                    'name_en' => $request->title_en,
                    'name_kh' => $request->title_km,
                    'description_en' => $request->description_en,
                    'description_kh' => $request->description_km,
                    'price' => $request->price ?? 0,
                    'stock' => $request->stock ?? 0,
                    'is_preorder' => (bool) $request->is_preorder,
                    'is_feature' => (bool) $request->is_feature,
                    'is_active' => $request->status === $this->active,
                    'has_variation' => is_array($request->product_variations) && count($request->product_variations) > 0,
                    'category_id' => $primaryCategoryId,
                    'product_source_id' => $request->product_source_id ?: null,
                    'product_location_id' => $request->product_location_id ?: null,
                    'source_en' => $request->source_en ?: null,
                    'source_kh' => $request->source_kh ?: null,
                ]);

                // Sync all categories to the pivot table
                $product->categories()->sync($categoryIds);

                $this->createVariations($product, $request);
                $this->syncImages($product, $request);
                $this->syncProductAttributes($product, $request);
            } else {

                /* ================= UPDATE ================= */

                $product = Product::findOrFail($request->id);

                $product->update([
                    'sku' => $request->code,
                    'name_en' => $request->title_en,
                    'name_kh' => $request->title_km,
                    'description_en' => $request->description_en,
                    'description_kh' => $request->description_km,
                    'price' => $request->price ?? 0,
                    'stock' => $request->stock ?? 0,
                    'is_preorder' => (bool) $request->is_preorder,
                    'is_feature' => (bool) $request->is_feature,
                    'is_active' => $request->status === $this->active,
                    'has_variation' => is_array($request->product_variations) && count($request->product_variations) > 0,
                    'category_id' => $primaryCategoryId,
                    'product_source_id' => $request->product_source_id ?: null,
                    'product_location_id' => $request->product_location_id ?: null,
                    'source_en' => $request->source_en ?: null,
                    'source_kh' => $request->source_kh ?: null,
                ]);

                // Sync all categories to the pivot table (removes old, adds new)
                $product->categories()->sync($categoryIds);

                /* ===== DELETE OLD VARIATIONS ===== */

                ProductVariation::where('product_id', $product->id)->delete();

                /* ===== RECREATE VARIATIONS ===== */

                $this->createVariations($product, $request);
                $this->syncImages($product, $request);
                $this->syncProductAttributes($product, $request);
            }

            DB::commit();
            return $this->responseSuccess();
        } catch (Exception $e) {
            DB::rollBack();
            return $this->responseError($e->getMessage());
        }
    }

    public function uploadImage(Request $request)
    {
        try {
            if (!$request->hasFile('file') || !$request->file('file')->isValid()) {
                return response()->json(['error' => true, 'message' => 'Invalid file'], 422);
            }
            $path = UploadFile::uploadFile('product/images', $request->file('file'));
            $publicPath = preg_replace('#^uploads/#', '', ltrim((string) $path, '/'));
            return response()->json([
                'error' => false,
                'path' => $path,
                'url'  => Storage::disk('uploads')->url($publicPath),
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()], 500);
        }
    }

    private function syncImages(Product $product, Request $request): void
    {
        if (!isset($request->images) || !is_array($request->images)) {
            return;
        }
        Gallery::where('foreign_id', $product->id)
            ->where('foreign_model', Product::class)
            ->delete();
        foreach ($request->images as $imagePath) {
            if ($imagePath) {
                Gallery::create([
                    'foreign_id'    => $product->id,
                    'foreign_model' => Product::class,
                    'image'         => $imagePath,
                    'user_id'       => auth()->id(),
                ]);
            }
        }
    }

    private function createVariations(Product $product, Request $request): void
    {
        if (!is_array($request->product_variations)) {
            return;
        }

        $usedSkus = [];

        foreach ($request->product_variations as $index => $variate) {
            $baseSku = trim((string) ($variate['sku'] ?? ''));
            if ($baseSku === '') {
                $baseSku = ($product->sku ?? ('P' . $product->id)) . '-' . ($index + 1);
            }

            $sku = $baseSku;
            $suffix = 1;
            while (
                in_array($sku, $usedSkus, true) ||
                ProductVariation::where('sku', $sku)->exists()
            ) {
                $sku = $baseSku . '-' . $suffix;
                $suffix++;
            }
            $usedSkus[] = $sku;

            ProductVariation::create([
                'product_id' => $product->id,
                'sku' => $sku,
                'barcode' => $variate['barcode'] ?? null,
                'name' => $variate['title_en'] ?? null,
                'combination_key' => $variate['combination_key'] ?? null,
                'price'  => $variate['price'] ?? 0,
                'stock' => $variate['stock'] ?? 0,
                'is_active' => ($variate['status'] ?? 'ACTIVE') === $this->active,
            ]);
        }
    }

    private function syncProductAttributes(Product $product, Request $request): void
    {
        $attributeIds = collect($request->input('product_attribute_ids', []))
            ->filter(fn($id) => !is_null($id) && $id !== '')
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values();

        DB::table('product_attribute_maps')
            ->where('product_id', $product->id)
            ->delete();

        if ($attributeIds->isEmpty()) {
            return;
        }

        $rows = $attributeIds->map(fn($attributeId) => [
            'product_id' => $product->id,
            'product_attribute_id' => $attributeId,
            'is_required' => false,
            'is_variation' => true,
            'created_at' => now(),
        ])->all();

        DB::table('product_attribute_maps')->insert($rows);
    }


    public function detail()
    {
        try {
            $data = Product::withRelation()->findOrFail(request('id'));
            return $this->responseSuccess($data);
        } catch (Exception $e) {
            return $this->responseError();
        }
    }

    public function updateStatus()
    {
        DB::beginTransaction();
        try {
            $data = Product::findOrFail(request('id'));
            $data->update([
                'is_active' => request('status') === $this->active
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
            $data = Product::findOrFail(request('id'));
            $data->delete();
            DB::commit();
            return $this->responseSuccess(null, __('form.message.delete.success'));
        } catch (Exception $e) {
            DB::rollBack();
            return $this->responseError();
        }
    }

    public function restore()
    {
        DB::beginTransaction();
        try {
            $data = Product::withTrashed()->findOrFail(request('id'));
            $data->restore();
            DB::commit();
            return $this->responseSuccess(null, __('form.message.restore.success'));
        } catch (Exception $e) {
            DB::rollBack();
            return $this->responseError();
        }
    }
}
