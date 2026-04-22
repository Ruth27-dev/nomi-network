<?php

namespace App\Http\Controllers\Admin\Pages;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Page\ProductionRequest;
use App\Models\Page;
use App\Models\UploadFile;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductionController extends Controller
{
    protected $module;

    public function __construct()
    {
        parent::__construct();
        $this->module = [
            'key' => config('dummy.module.production.key'),
        ];
        $this->middleware('permission:production-view', ['only' => ['index']]);
        $this->middleware('permission:production-update', ['only' => ['save']]);
    }

    public function index()
    {
        $data['page'] = Page::where('page', 'production')->first();
        return view("admin::pages.page.production", $data);
    }

    public function save(ProductionRequest $request)
    {
        DB::beginTransaction();
        try {
            $input = [
                'page' => $request->page,
                'content' => [
                    'dataDetail' => collect($request->dataDetail)->map(function ($item, $index) use ($request) {
                        $detail = [
                            'description_en' => $item['description_en'] ?? null,
                            'description_km' => $item['description_km'] ?? null,
                            'ordering' => $item['ordering'] ?? null,
                            'image' => $item['tmp_image'] ?? null,
                        ];

                        if ($request->hasFile("dataDetail.$index.image")) {
                            $detail['image'] = UploadFile::uploadFile(
                                '/list-of-value',
                                $request->file("dataDetail.$index.image")
                            );
                        }

                        return $detail;
                    }),
                    'imageSlides' => collect($request->imageSlides)->map(function ($item, $index) use ($request) {
                        $slide = [
                            'ordering' => $item['ordering'] ?? null,
                            'image' => $item['tmp_image'] ?? null,
                        ];

                        if ($request->hasFile("imageSlides.$index.image")) {
                            $slide['image'] = UploadFile::uploadFile(
                                '/list-of-value',
                                $request->file("imageSlides.$index.image")
                            );
                        }

                        return $slide;
                    }),
                ],
                'status' => $request->status,
                'user_id' => Auth::user()->id,
            ];

            if (!$request->id) {
                $data = Page::create($input);
            } else {
                $data = Page::find($request->id);
                $data->update($input);
            }

            DB::commit();
            $data->refresh();

            return response()->json([
                'status' => 'success',
                'message' => $request->id
                    ? __('form.message.update.success')
                    : __('form.message.create.success'),
                'error' => false,
                'id' => $data->id,
                'data' => $data,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => __('form.message.error'),
                'error' => true,
                'exception' => $e->getMessage(),
            ]);
        }
    }
}
