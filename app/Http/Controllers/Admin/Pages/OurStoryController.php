<?php

namespace App\Http\Controllers\Admin\Pages;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Page\OurStoryRequest;
use App\Models\Page;
use App\Models\UploadFile;
use App\Services\ActivityLogService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OurStoryController extends Controller
{
    protected $module;
    public function __construct()
    {
        parent::__construct();
        $this->module = [
            'key'   => config('dummy.module.our_story.key'),
        ];
        $this->middleware('permission:our-story-view', ['only' => ['index', 'data']]);
        $this->middleware('permission:our-story-update', ['only' => ['save']]);
    }

    public function index()
    {
        $data['page'] = Page::where('page', 'our_story')->first();
        return view("admin::pages.page.our-story", $data);
    }

    public function save(OurStoryRequest $request)
    {
        DB::beginTransaction();
        try {
            $image = UploadFile::uploadFile('/list-of-value', $request->file('image'));

            $input = [
                'page'    => $request->page,
                'image'   => $image,
                'title' => [
                    'en' => $request->title_en,
                    'km' => $request->title_km,
                ],
                'content' => [
                    'en' => $request->content_en,
                    'km' => $request->content_km,
                ],
                'status'        => $request->status,
                'user_id' => Auth::user()->id,
            ];

            if (!$request->id) {
                $data = Page::create($input);
            } else {
                $data = Page::find($request->id);
                if ($request->hasFile('image') || !$request->tmp_file) {
                    UploadFile::deleteFile('/list-of-value', $data->image);
                }
                $input['image'] =  $image ?? $request->tmp_file;
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
