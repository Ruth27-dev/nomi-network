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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
            'categories' => Category::query()->where('status', $this->active)->get(),
            'type'      => request('type'),
        ];

        return view('admin::pages.product.index', $data);
    }

    public function data()
    {
        try {
            $pag = request('pag') ?? 50;
            $data = Product::query()
                ->when(request('status'), fn($q) => $q->where('status', request('status')))
                ->when(request('trash'), fn($q) => $q->onlyTrashed())
                ->when(request('category_id'), function ($q) {
                    $q->whereHas('categories', fn($cat) => $cat->where('categories.id', request('category_id')));
                })
                ->when(request('search'), function ($q) {
                    $q->where(function ($q) {
                        $q->where('code', 'LIKE', '%' . request('search') . '%');
                        $q->orWhere('title->en', 'LIKE', '%' . request('search') . '%');
                        $q->orWhere('title->km', 'LIKE', '%' . request('search') . '%');
                        $q->orWhere('description->en', 'LIKE', '%' . request('search') . '%');
                        $q->orWhere('description->km', 'LIKE', '%' . request('search') . '%');
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

            $image = UploadFile::uploadFile(
                '/product',
                $request->file('image'),
                $request->tmp_file
            );

            if (!$request->id) {

                /* ================= CREATE ================= */

                $product = Product::create([
                    'code'        => $request->code,
                    'title'       => [
                        'en' => $request->title_en,
                        'km' => $request->title_km,
                    ],
                    'status'      => $request->status,
                    'type'        => $request->display_type,
                    'description' => [
                        'en' => $request->description_en,
                        'km' => $request->description_km,
                    ],
                    'image'       => $image,
                    'user_id'     => Auth::id(),
                ]);

                $product->categories()->sync($request->category_ids ?? []);

                $this->createVariations($product, $request);
            } else {

                /* ================= UPDATE ================= */

                $product = Product::findOrFail($request->id);

                $product->update([
                    'code'        => $request->code,
                    'title'       => [
                        'en' => $request->title_en,
                        'km' => $request->title_km,
                    ],
                    'status'      => $request->status,
                    'type'        => $request->display_type,
                    'description' => [
                        'en' => $request->description_en,
                        'km' => $request->description_km,
                    ],
                    'image'       => $image,
                ]);

                $product->categories()->sync($request->category_ids ?? []);

                /* ===== DELETE OLD VARIATIONS ===== */

                ProductVariation::where('product_id', $product->id)->delete();

                /* ===== RECREATE VARIATIONS ===== */

                $this->createVariations($product, $request);

                $this->cleanupOrphanVariationImages($product);
            }

            DB::commit();
            return $this->responseSuccess();
        } catch (Exception $e) {
            DB::rollBack();
            return $this->responseError($e->getMessage());
        }
    }

    private function createVariations(Product $product, Request $request): void
    {
        if (!is_array($request->product_variations)) {
            return;
        }

        foreach ($request->product_variations as $index => $variate) {

            $variation = ProductVariation::create([
                'product_id' => $product->id,
                'title' => [
                    'en' => $variate['title_en'] ?? null,
                    'km' => $variate['title_km'] ?? null,
                ],
                'status' => $variate['status'] ?? 'ACTIVE',
                'price'  => $variate['price'] ?? 0,
                'size'   => $variate['size'] ?? null,
                'description' => [
                    'en' => $variate['description_en'] ?? null,
                    'km' => $variate['description_km'] ?? null,
                ],
                'note' => [
                    'en' => $variate['note_en'] ?? null,
                    'km' => $variate['note_km'] ?? null,
                ],
                'is_available' => filter_var($variate['is_available'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'is_note'      => filter_var($variate['is_note'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'user_id'      => Auth::id(),
            ]);

            /* ===== REATTACH OLD IMAGES ===== */

            if (!empty($variate['tmp_files']) && is_array($variate['tmp_files'])) {
                Gallery::whereIn('image', $variate['tmp_files'])
                    ->update([
                        'foreign_id'    => $variation->id,
                        'foreign_model' => ProductVariation::class,
                    ]);
            }

            /* ===== UPLOAD NEW IMAGES ===== */

            if ($request->hasFile("product_variations.$index.images")) {
                foreach ($request->file("product_variations.$index.images") as $file) {

                    $path = UploadFile::uploadFile('/product/variation', $file, null);

                    Gallery::create([
                        'foreign_id'    => $variation->id,
                        'foreign_model' => ProductVariation::class,
                        'image'         => $path,
                        'user_id'       => Auth::id(),
                    ]);
                }
            }
        }
    }

    private function cleanupOrphanVariationImages(): void
    {
        $orphans = Gallery::where('foreign_model', ProductVariation::class)
            ->whereNotIn('foreign_id', function ($query) {
                $query->select('id')
                    ->from('product_variations');
            })
            ->get();

        foreach ($orphans as $gallery) {
            // delete physical file
            UploadFile::deleteFile('/product/variation', $gallery->image);
            // delete database record
            $gallery->delete();
        }
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
                'status' => request('status')
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
