<?php

namespace App\Http\Controllers\Admin\Pages;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Page\UpcomingEventRequest;
use App\Models\Page;
use App\Models\UploadFile;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UpcomingEventController extends Controller
{
    protected $module;

    public function __construct()
    {
        parent::__construct();
        $this->module = [
            'key' => config('dummy.module.upcoming_event.key'),
        ];
        $this->middleware('permission:upcoming-event-view', ['only' => ['index', 'data']]);
        $this->middleware('permission:upcoming-event-update', ['only' => ['save']]);
    }

    public function index()
    {
        $data['page'] = Page::where('page', 'upcoming_event')->first();
        return view("admin::pages.page.upcoming-event", $data);
    }

    public function save(UpcomingEventRequest $request)
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
                            'date' => !empty($item['date'])
                                ? Carbon::createFromFormat('d/m/Y', $item['date'])->format('Y-m-d')
                                : null,
                            'location_en' => $item['location_en'] ?? null,
                            'location_km' => $item['location_km'] ?? null,
                            'description_en' => $item['description_en'] ?? null,
                            'description_km' => $item['description_km'] ?? null,
                            'is_upcoming_event' => filter_var($item['is_upcoming_event'] ?? false, FILTER_VALIDATE_BOOLEAN),
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
