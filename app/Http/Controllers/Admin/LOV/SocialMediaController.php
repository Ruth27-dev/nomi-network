<?php

namespace App\Http\Controllers\Admin\LOV;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SocialMediaRequest;
use App\Models\SocialMedia;
use App\Models\UploadFile;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SocialMediaController extends Controller
{
    protected $module;
    public function __construct()
    {
        parent::__construct();
        $this->module = [
            'key'   => config('dummy.module.social_media.key'),
        ];
        $this->middleware('permission:social-media-view', ['only' => ['index', 'data']]);
        $this->middleware('permission:social-media-create|social-media-update', ['only' => ['save']]);
        $this->middleware('permission:social-media-update', ['only' => ['onUpdateStatus']]);
        $this->middleware('permission:social-media-delete', ['only' => ['onDelete', 'onDestroy']]);
        $this->middleware('permission:social-media-restore', ['only' => ['onRestore']]);
    }

    public function index()
    {
        return view("admin::pages.setting.social-media.index");
    }

    public function data()
    {
        try {
            $pag = request('pag') ?? 50;
            $data = SocialMedia::query()
                ->when(request('status'), function ($q) {
                    $q->where('status', request('status'));
                })
                ->when(request('trash'), function ($q) {
                    $q->onlyTrashed();
                })
                ->when(request('search'), function ($q) {
                    $q->where(function ($q) {
                        $q->where('title->en', 'LIKE', '%' . request('search') . '%');
                        $q->orWhere('title->km', 'LIKE', '%' . request('search') . '%');
                        $q->orWhere('url', 'LIKE', '%' . request('search') . '%');
                    });
                })
                ->orderBy('ordering')
                ->orderByDesc('created_at')
                ->paginate($pag);
            return $data;
        } catch (Exception $e) {
            return $this->responseError();
        }
    }

    public function save(SocialMediaRequest $request)
    {
        $permission = $request->id ? 'social-media-update' : 'social-media-create';
        abort_unless(Auth::guard('admin')->user()?->can($permission), 403);

        DB::beginTransaction();
        try {
            $image = UploadFile::uploadFile('/social-media', $request->file('image'));
            $input = [
                'title'         => [
                    'en'    => $request->title_en,
                    'km'    => $request->title_km,
                ],
                'ordering'         => $request->ordering,
                'image'            => $image,
                'url'              => $request->url,
                'status'           => $request->status,
                'user_id'          => Auth::user()->id,
            ];

            if (!$request->id) {
                SocialMedia::create($input);
            } else {
                $data = SocialMedia::findOrFail($request->id);
                if ($request->file('image') || !$request->tmp_file) {
                    UploadFile::deleteFile('/social-media', $data->image);
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
            $user = SocialMedia::find($request->id);
            $user->update(['status' => $request->status]);
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
            SocialMedia::find($request->id)->delete();
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
            SocialMedia::onlyTrashed()->find($request->id)->restore();
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
            SocialMedia::onlyTrashed()->find($request->id)->forceDelete();
            return response()->json([
                'status' => 'success',
                'message' => __('form.message.delete.success'),
                'error' => false,
            ]);
        } catch (\Exception $e) {
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
            $data['max_ordering'] = SocialMedia::max('ordering') + 1;
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
