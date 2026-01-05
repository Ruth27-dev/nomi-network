<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BannerRequest;
use App\Models\Banner;
use App\Models\UploadFile;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BannerController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:banner-view', ['only' => ['index', 'data']]);
        $this->middleware('permission:banner-create', ['only' => ['save']]);
        $this->middleware('permission:banner-update', ['only' => ['save', 'onUpdateStatus']]);
        $this->middleware('permission:banner-delete', ['only' => ['onDelete']]);
        $this->middleware('permission:banner-restore', ['only' => ['onRestore']]);
    }

    public function index()
    {
        return view('admin::pages.setting.banner.index');
    }

    public function data()
    {
        $data = Banner::query()
            ->when(filled(request('search')), function ($q) {
                $search = '%' . request('search') . '%';
                $q->where(function ($query) use ($search) {
                    $query->where('title->en', 'like', $search)
                        ->orWhere('title->km', 'like', $search)
                        ->orWhere('banner_page', 'like', $search)
                        ->orWhere('url', 'like', $search);
                });
            })->when(request('trash'), function ($q) {
                $q->onlyTrashed();
            })
            ->orderByDesc('created_at')
            ->paginate(25);

        return response()->json(['data' => $data]);
    }

    public function save(BannerRequest $request)
    {
        DB::beginTransaction();
        try {
            $image = UploadFile::uploadFile('/banner', $request->file('image'));
            $input = [
                'title' => [
                    'en' => $request->title_en,
                    'km' => $request->title_km,
                ],
                'banner_page' => $request->banner_page,
                'ordering' => $request->ordering,
                'status' => $request->status,
                'url' => $request->url,
                'image' => $image,
                'user_id' => Auth::user()->id,
            ];

            if (!$request->id) {
                Banner::create($input);
            } else {
                $data = Banner::findOrFail($request->id);
                if ($request->file('image') || !$request->tmp_file) {
                    UploadFile::deleteFile('/banner', $data->image);
                }
                $input['image'] = $image ?? $request->tmp_file;
                $data->update($input);
            }

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => $request->id ? __('form.message.update.success') : __('form.message.create.success'),
                'error' => false,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => __('form.message.error'),
                'error' => true,
            ]);
        }
    }

    public function onUpdateStatus(Request $request)
    {
        try {
            $data = Banner::find($request->id);
            $data->update(['status' => $request->status]);
            return response()->json([
                'status' => 'success',
                'message' => __('form.message.status.success'),
                'error' => false,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => __('form.message.error'),
                'error' => true,
            ]);
        }
    }

    public function onDelete(Request $request)
    {
        try {
            Banner::find($request->id)->delete();
            return response()->json([
                'status' => 'success',
                'message' => __('form.message.move_to_trash.success'),
                'error' => false,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => __('form.message.error'),
                'error' => true,
            ]);
        }
    }

    public function onRestore(Request $request)
    {
        try {
            Banner::onlyTrashed()->find($request->id)->restore();
            return response()->json([
                'status' => 'success',
                'message' => __('form.message.restore.success'),
                'error' => false,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => __('form.message.error'),
                'error' => true,
            ]);
        }
    }

    public function onDestroy(Request $request)
    {
        try {
            Banner::onlyTrashed()->find($request->id)->forceDelete();
            return response()->json([
                'status' => 'success',
                'message' => __('form.message.delete.success'),
                'error' => false,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => __('form.message.error'),
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function getMaxOrdering()
    {
        try {
            $data['max_ordering'] = Banner::max('ordering') + 1;
            return response()->json($data);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
