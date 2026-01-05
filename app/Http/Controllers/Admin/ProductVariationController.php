<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductVariationRequest;
use App\Models\Gallery;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\UploadFile;
use Exception;
use Illuminate\Support\Facades\Auth;
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
            ->where('status', $this->active)
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
                ->when(request('status'), fn($q) => $q->where('status', request('status')))
                ->when(request('product_id'), fn($q) => $q->where('product_id', request('product_id')))
                ->when(request('search'), function ($q) {
                    $q->where(function ($query) {
                        $query->where('title->en', 'LIKE', '%' . request('search') . '%');
                        $query->orWhere('title->km', 'LIKE', '%' . request('search') . '%');
                        $query->orWhere('description->en', 'LIKE', '%' . request('search') . '%');
                        $query->orWhere('description->km', 'LIKE', '%' . request('search') . '%');
                        $query->orWhereHas('product', function ($product) {
                            $product->where('title->en', 'LIKE', '%' . request('search') . '%');
                            $product->orWhere('title->km', 'LIKE', '%' . request('search') . '%');
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
                'title' => [
                    'en' => $request->title_en,
                    'km' => $request->title_km,
                ],
                'status' => $request->status,
                'price' => $request->price,
                'size' => $request->size,
                'description' => [
                    'en' => $request->description_en,
                    'km' => $request->description_km,
                ],
                'note' => [
                    'en' => $request->note_en,
                    'km' => $request->note_km,
                ],
                'is_available' => true,
                'user_id' => Auth::id(),
            ];

            if (!$request->id) {
                $variation = ProductVariation::create($payload);
            } else {
                $variation = ProductVariation::findOrFail($request->id);
                $variation->update($payload);
            }

            $this->syncVariationImages($variation, $request);

            DB::commit();
            return $this->responseSuccess();
        } catch (Exception $e) {
            DB::rollBack();
            return $this->responseError();
        }
    }

    private function syncVariationImages(ProductVariation $variation, ProductVariationRequest $request): void
    {
        $keepImages = $request->input('tmp_files', []);
        if (!is_array($keepImages)) {
            $keepImages = [];
        }

        $existing = Gallery::where('foreign_model', ProductVariation::class)
            ->where('foreign_id', $variation->id)
            ->get();

        foreach ($existing as $gallery) {
            if (!in_array($gallery->image, $keepImages, true)) {
                UploadFile::deleteFile('/product/variation', $gallery->image);
                $gallery->delete();
            }
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = UploadFile::uploadFile('/product/variation', $file, null);
                Gallery::create([
                    'foreign_id' => $variation->id,
                    'foreign_model' => ProductVariation::class,
                    'image' => $path,
                    'user_id' => Auth::id(),
                ]);
            }
        }
    }

    public function updateStatus()
    {
        DB::beginTransaction();
        try {
            $data = ProductVariation::findOrFail(request('id'));
            $data->update([
                'status' => request('status'),
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
            $variation = ProductVariation::findOrFail(request('id'));
            $images = Gallery::where('foreign_model', ProductVariation::class)
                ->where('foreign_id', $variation->id)
                ->get();

            foreach ($images as $gallery) {
                UploadFile::deleteFile('/product/variation', $gallery->image);
                $gallery->delete();
            }

            $variation->delete();
            DB::commit();
            return $this->responseSuccess(null, __('form.message.delete.success'));
        } catch (Exception $e) {
            DB::rollBack();
            return $this->responseError();
        }
    }
}
