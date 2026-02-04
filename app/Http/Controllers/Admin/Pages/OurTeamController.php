<?php

namespace App\Http\Controllers\Admin\Pages;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Page\OurTeamRequest;
use App\Models\Page;
use App\Models\UploadFile;
use App\Services\ActivityLogService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OurTeamController extends Controller
{
    protected $module;
    public function __construct()
    {
        parent::__construct();
        $this->module = [
            'key'   => config('dummy.module.our_team.key'),
        ];
        $this->middleware('permission:our-team-view', ['only' => ['index', 'data']]);
        $this->middleware('permission:our-team-update', ['only' => ['save']]);
    }

    public function index()
    {
        $data['page'] = Page::where('page', 'our_team')->first();
        return view("admin::pages.page.our-team", $data);
    }

    public function save(OurTeamRequest $request)
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
                    'dataDetail' => collect($request->dataDetail)->map(function ($item, $index) use ($request) {
                        $detail = [
                            'name_en' => $item['name_en'] ?? null,
                            'name_km' => $item['name_km'] ?? null,
                            'position_en' => $item['position_en'] ?? null,
                            'position_km' => $item['position_km'] ?? null,
                            'description_en' => $item['description_en'] ?? null,
                            'description_km' => $item['description_km'] ?? null,
                            'ordering' => $item['ordering'] ?? null,
                            'profile' => $item['tmp_profile'] ?? null,
                        ];

                        if ($request->hasFile("dataDetail.$index.profile")) {
                            $detail['profile'] = UploadFile::uploadFile('/list-of-value', $request->file("dataDetail.$index.profile"));
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
