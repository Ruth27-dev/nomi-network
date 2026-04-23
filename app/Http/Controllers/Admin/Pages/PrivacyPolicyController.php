<?php

namespace App\Http\Controllers\Admin\Pages;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PageRequest;
use App\Models\Page;
use App\Services\ActivityLogService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PrivacyPolicyController extends Controller
{
    protected $module;
    public function __construct()
    {
        parent::__construct();
        $this->module = [
            'key'   => config('dummy.module.privacy_policy.key'),
        ];
        $this->middleware('permission:privacy-policy-view', ['only' => ['index', 'data']]);
        $this->middleware('permission:privacy-policy-update', ['only' => ['save']]);
    }


    public function index()
    {
        $data['page'] = Page::where('page', 'privacy_policy')->first();
        return view("admin::pages.page.privacy-policy", $data);
    }


    public function save(PageRequest $request)
    {
        DB::beginTransaction();
        try {
            $input = [
                'page'    => $request->page,
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
            dd($e);
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
