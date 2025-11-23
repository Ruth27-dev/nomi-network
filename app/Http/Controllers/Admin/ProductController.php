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

        return view('admin::pages.item.index', $data);
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
            $category_ids = $request->category_ids;
            $galleries = $request->file('galleries');
            $image = UploadFile::uploadFile('/product', $request->file('image'), $request->tmp_file);
            $input = [
                'code'          => $request->code,
                'title'         => [
                    'en'    => $request->title_en,
                    'km'    => $request->title_km,
                ],
                'unit_id'       => $request->unit_id,
                'is_sellable'   => $request->is_sellable == 'true' ? true : false,
                'is_consumable' => $request->is_consumable == 'true' ? true : false,
                'status'        => $request->status,
                'type'          => $request->display_type,
                'description'   => [
                    'en'    => $request->description_en,
                    'km'    => $request->description_km,
                ],
                'image'         => $image,
                'user_id'       => Auth::user()->id,
            ];
            if (!$request->id) {
                $data = Product::create($input);
                $data->categories()->sync($category_ids);
                if (isset($galleries)) {
                    if (isset($galleries)) {
                        foreach ($galleries as $key => $gallery) {
                            $img = UploadFile::uploadFile('/product', $gallery['img'], null);
                            Gallery::create([
                                'foreign_id'    => $data->id,
                                'foreign_model' => Product::class,
                                'image'         => $img,
                                'title'         => null,
                                'description'   => null,
                                'user_id'       => Auth::user()->id,
                            ]);
                        }
                    }
                }
                if ($request->product_variation && count($request->product_variation)) {
                    foreach ($request->product_variation as $index => $variate) {
                        $image_file = $request->file("product_variation.$index.image");
                        $detail_img =  UploadFile::uploadFile('/product', $image_file, $variate['tmp_file']);
                        $detail = ProductVariation::create([
                            'product_id'   => $data->id,
                            'title'     => [
                                'en'    => $variate['title_en'],
                                'km'    => $variate['title_km'],
                            ],
                            'status'    => $variate['status'],
                            'image'     => $detail_img,
                            'price'             => $variate['price'],
                            'size'              => $variate['size'],
                            'description'       => [
                                'en'    => $variate['description_en'],
                                'km'    => $variate['description_km'],
                            ],
                            'note'              => [
                                'en'    => $variate['note_en'],
                                'km'    => $variate['note_km'],
                            ],
                            'is_available'      => $variate['is_available'] == 'true' ? true : false,
                            'is_note'           => $variate['is_note'] == 'true' ? true : false,
                            'user_id'           => Auth::user()->id,
                        ]);
                    }
                }
            } else {
                $gallery_ids = [];
                $data = Product::find($request->id);
                $data->categories()->sync($category_ids);
                if ($request->file('image')) {
                    UploadFile::deleteFile('/product', $data->image);
                }
                $data->update($input);
                if (isset($galleries)) {
                    foreach ($galleries as $key => $gallery) {
                        $id = $request->input("galleries.$key.id");
                        $img = UploadFile::uploadFile('/product', $gallery['img'], null);
                        if ($id) {
                            $gallery_ids[] = $id;
                            $gal = Gallery::findOrFail($id);
                            if ($gal) {
                                UploadFile::deleteFile('/product', $gal->image);
                                $gal->update([
                                    'image'     => $img,
                                    'user_id'   => Auth::user()->id,
                                ]);
                            }
                        } else {
                            $gal = Gallery::create([
                                'foreign_id'    => $data->id,
                                'foreign_model' => Product::class,
                                'image'         => $img,
                                'title'         => null,
                                'description'   => null,
                                'user_id'       => Auth::user()->id,
                            ]);
                            $gallery_ids[] = $gal->id;
                        }
                    }
                }
                $discard_galleries = $data->galleries->whereNotIn('id', $gallery_ids);
                if (count($discard_galleries)) {
                    foreach ($discard_galleries as $key => $discard) {
                        UploadFile::deleteFile('/product', $discard->image);
                        $discard->delete();
                    }
                }
            }

            DB::commit();
            return $this->responseSuccess();
        } catch (Exception $e) {
            DB::rollBack();
            return $this->responseError();
        }
    }

    public function detail()
    {
        return (new ItemService)->detail(request('id'));
    }

    public function updateStatus()
    {
        return (new ItemService)->updateStatus(request('id'), request('store'));
    }

    public function delete()
    {
        return (new ItemService)->delete(request('id'));
    }

    public function restore()
    {
        return (new ItemService)->restore(request('id'));
    }
}
