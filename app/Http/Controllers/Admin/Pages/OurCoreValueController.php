<?php

namespace App\Http\Controllers\Admin\Pages;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Page\OurCoreValueRequest;
use App\Models\Page;
use App\Models\UploadFile;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OurCoreValueController extends Controller
{
    protected $module;

    public function __construct()
    {
        parent::__construct();
        $this->module = [
            'key' => config('dummy.module.our_core_value.key'),
        ];
        $this->middleware('permission:our-core-value-view', ['only' => ['index', 'data']]);
        $this->middleware('permission:our-core-value-update', ['only' => ['save']]);
    }

    public function index()
    {
        $data['page'] = Page::where('page', 'our_core_value')->first();
        return view("admin::pages.page.our-core-value", $data);
    }

    public function save(OurCoreValueRequest $request)
    {
        DB::beginTransaction();
        try {
            $input = [
                'page' => $request->page,
                'title' => [
                    'en' => $request->title_en,
                    'km' => $request->title_km,
                ],
                'short_detail' => [
                    'en' => $request->short_detail_en,
                    'km' => $request->short_detail_km,
                ],
                'content' => [
                    'dataDetail' => collect($request->dataDetail)->map(function ($item, $index) use ($request) {
                        $detail = [
                            'title_en' => $item['title_en'] ?? null,
                            'title_km' => $item['title_km'] ?? null,
                            'description_en' => $item['description_en'] ?? null,
                            'description_km' => $item['description_km'] ?? null,
                            'ordering' => $item['ordering'] ?? null,
                            'icon' => $item['tmp_icon'] ?? null,
                        ];

                        if ($request->hasFile("dataDetail.$index.icon")) {
                            $detail['icon'] = UploadFile::uploadFile(
                                '/list-of-value',
                                $request->file("dataDetail.$index.icon")
                            );
                        }

                        return $detail;
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
