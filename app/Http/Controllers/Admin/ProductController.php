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

            $product = Product::updateOrCreate(
                ['id' => $request->id],
                [
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
                ]
            );

            $product->categories()->sync($request->category_ids ?? []);

            /* ================= PRODUCT VARIATES ================= */

            if ($request->product_variations && is_array($request->product_variations)) {

                foreach ($request->product_variations as $index => $variate) {

                    $variation = ProductVariation::updateOrCreate(
                        ['id' => $variate['product_variate_id'] ?? null],
                        [
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
                        ]
                    );

                    /* ================= VARIATE IMAGES ================= */

                    $keepImages = $variate['tmp_files'] ?? [];

                    // delete removed images
                    Gallery::where('foreign_model', ProductVariation::class)
                        ->where('foreign_id', $variation->id)
                        ->whereNotIn('image', $keepImages)
                        ->delete();

                    // upload new images
                    if ($request->hasFile("product_variations.$index.images")) {
                        foreach ($request->file("product_variations.$index.images") as $file) {

                            $path = UploadFile::uploadFile('/product/variation', $file, null);

                            Gallery::create([
                                'foreign_id'    => $variation->id,
                                'foreign_model' => ProductVariation::class,
                                'image'         => $path,
                                'title'         => null,
                                'description'   => null,
                                'user_id'       => Auth::id(),
                            ]);
                        }
                    }
                }
            }

            DB::commit();
            return $this->responseSuccess();
        } catch (Exception $e) {
            DB::rollBack();
            return $this->responseError($e->getMessage());
        }
    }


    public function detail()
    {
        try {
            $data = Product::withRelation()->findOrFail(request('id'));
            return $this->responseSuccess($data);
        } catch (Exception $e) {
            dd($e);
            return $this->responseError();
        }
    }

    // public function updateStatus()
    // {
    //     return (new ItemService)->updateStatus(request('id'), request('store'));
    // }

    // public function delete()
    // {
    //     return (new ItemService)->delete(request('id'));
    // }

    // public function restore()
    // {
    //     return (new ItemService)->restore(request('id'));
    // }
}
