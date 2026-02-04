<?php

namespace App\Http\Controllers\Admin\Pages;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Page\FrequentlyAskedQuestionRequest;
use App\Models\Page;
use App\Services\ActivityLogService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FrequentlyAskedQuestionController extends Controller
{
    protected $module;
    public function __construct()
    {
        parent::__construct();
        $this->module = [
            'key'   => config('dummy.module.frequently_asked_question.key'),
        ];
        $this->middleware('permission:frequently-asked-question-view', ['only' => ['index', 'data']]);
        $this->middleware('permission:frequently-asked-question-update', ['only' => ['save']]);
    }

    public function index()
    {
        $data['page'] = Page::where('page', 'frequently_asked_question')->first();
        return view("admin::pages.page.frequently-asked-question", $data);
    }

    public function save(FrequentlyAskedQuestionRequest $request)
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
                            'question_en' => $item['question_en'] ?? null,
                            'question_km' => $item['question_km'] ?? null,
                            'answer_en' => $item['answer_en'] ?? null,
                            'answer_km' => $item['answer_km'] ?? null,
                            'ordering' => $item['ordering'] ?? null,
                        ];

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
