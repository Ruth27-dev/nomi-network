<?php

namespace App\Http\Controllers\Admin\Pages;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Page\MissionVisionRequest;
use App\Models\ListOfValue;
use App\Models\UploadFile;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MissionVisionController extends Controller
{
    protected string $type;

    public function __construct()
    {
        parent::__construct();

        $this->type = config('dummy.module.mission_vision.key');

        $this->middleware('permission:mission-vision-view', ['only' => ['index', 'data']]);
        $this->middleware('permission:mission-vision-create|mission-vision-update', ['only' => ['save']]);
        $this->middleware('permission:mission-vision-update', ['only' => ['onUpdateStatus']]);
        $this->middleware('permission:mission-vision-delete', ['only' => ['onDelete', 'onDestroy']]);
        $this->middleware('permission:mission-vision-restore', ['only' => ['onRestore']]);
    }

    public function index()
    {
        return view('admin::pages.page.mission-vision.index');
    }

    public function data()
    {
        $data = ListOfValue::query()
            ->where('type', $this->type)
            ->when(filled(request('search')), function ($q) {
                $search = '%' . request('search') . '%';
                $q->where(function ($query) use ($search) {
                    $query->where('title->en', 'like', $search)
                        ->orWhere('title->km', 'like', $search)
                        ->orWhere('description->en', 'like', $search)
                        ->orWhere('description->km', 'like', $search)
                        ->orWhere('add_on->type', 'like', $search);
                });
            })
            ->when(request('trash'), function ($q) {
                $q->onlyTrashed();
            })
            ->orderBy('sequence')
            ->orderByDesc('created_at')
            ->paginate(25);

        return response()->json(['data' => $data]);
    }

    public function save(MissionVisionRequest $request)
    {
        $permission = $request->id ? 'mission-vision-update' : 'mission-vision-create';
        abort_unless(Auth::guard('admin')->user()?->can($permission), 403);

        DB::beginTransaction();
        try {
            $newImages = [];
            if ($request->hasFile('images')) {
                foreach ((array) $request->file('images') as $img) {
                    if ($img && $img->isValid()) {
                        $newImages[] = UploadFile::uploadFile('/list-of-value', $img);
                    }
                }
            }

            $keptImages = array_values(array_filter((array) $request->input('tmp_images', [])));
            $allImages = array_merge($newImages, $keptImages);

            $input = [
                'type' => $this->type,
                'title' => [
                    'en' => $request->title_en,
                    'km' => $request->title_km,
                ],
                'description' => [
                    'en' => $request->description_en,
                    'km' => $request->description_km,
                ],
                'add_on' => [
                    'type'   => $request->type,
                    'images' => $allImages,
                ],
                'sequence' => $request->sequence,
                'status'   => $request->status,
                'image'    => $allImages[0] ?? null,
                'user_id'  => Auth::guard('admin')->id(),
            ];

            if (!$request->id) {
                ListOfValue::create($input);
            } else {
                $data = ListOfValue::where('type', $this->type)->findOrFail($request->id);
                $oldImages = data_get($data->add_on, 'images', $data->image ? [$data->image] : []);
                foreach ($oldImages as $oldImg) {
                    if (!in_array($oldImg, $keptImages, true)) {
                        UploadFile::deleteFile('/list-of-value', $oldImg);
                    }
                }
                $data->update($input);
            }

            DB::commit();
            return response()->json([
                'status'  => 'success',
                'message' => $request->id ? __('form.message.update.success') : __('form.message.create.success'),
                'error'   => false,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => __('form.message.error'),
                'error'   => true,
            ]);
        }
    }

    public function onUpdateStatus(Request $request)
    {
        try {
            $data = ListOfValue::where('type', $this->type)->findOrFail($request->id);
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
            ListOfValue::where('type', $this->type)->findOrFail($request->id)->delete();
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
            ListOfValue::onlyTrashed()->where('type', $this->type)->findOrFail($request->id)->restore();
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
            $data = ListOfValue::onlyTrashed()->where('type', $this->type)->findOrFail($request->id);
            $allImages = data_get($data->add_on, 'images', $data->image ? [$data->image] : []);
            foreach ($allImages as $img) {
                UploadFile::deleteFile('/list-of-value', $img);
            }
            $data->forceDelete();

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
            $data['max_ordering'] = ListOfValue::where('type', $this->type)->max('sequence') + 1;
            return response()->json($data);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
