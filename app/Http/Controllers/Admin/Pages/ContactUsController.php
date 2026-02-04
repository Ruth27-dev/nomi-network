<?php

namespace App\Http\Controllers\Admin\Pages;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Page\ContactUsRequest;
use App\Models\Page;
use App\Models\UploadFile;
use App\Services\ActivityLogService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ContactUsController extends Controller
{
    protected $module;
    public function __construct()
    {
        parent::__construct();
        $this->module = [
            'key'   => config('dummy.module.contact_us.key'),
        ];
        $this->middleware('permission:contact-us-view', ['only' => ['index', 'data']]);
        $this->middleware('permission:contact-us-update', ['only' => ['save']]);
    }

    public function index()
    {
        $data['page'] = Page::where('page', 'contact_us')->first();
        return view("admin::pages.page.contact-us", $data);
    }

    public function save(ContactUsRequest $request)
    {
        DB::beginTransaction();
        try {
            $input = [
                'page'    => $request->page,
                'title' => [
                    'en' => $request->title_en,
                    'km' => $request->title_km,
                ],
                'short_detail' => [
                    'en' => $request->short_detail_en,
                    'km' => $request->short_detail_km,
                ],
                'content' => [
                    'embed_map' => $request->embed_map,
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
                            $detail['icon'] = UploadFile::uploadFile('/list-of-value', $request->file("dataDetail.$index.icon"));
                        }

                        return $detail;
                    }),
                ],
                'status'        => $request->status,

                'user_id' => Auth::user()->id,
            ];

            if (!$request->id) {
                $data = Page::create($input);
            } else {
                $data = Page::find($request->id);
                $data->update($input);
            }

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => $request->id
                    ? __('form.message.update.success')
                    : __('form.message.create.success'),
                'error' => false,
                'id' => $data->id,
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
