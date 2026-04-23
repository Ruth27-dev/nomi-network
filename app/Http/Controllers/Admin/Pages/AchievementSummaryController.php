<?php

namespace App\Http\Controllers\Admin\Pages;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Page\AchievementSummaryRequest;
use App\Models\ListOfValue;
use App\Models\UploadFile;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AchievementSummaryController extends Controller
{
    protected string $type;

    public function __construct()
    {
        parent::__construct();

        $this->type = config('dummy.module.achievement_summary.key');

        $this->middleware('permission:achievement-summary-view', ['only' => ['index', 'data']]);
        $this->middleware('permission:achievement-summary-create|achievement-summary-update', ['only' => ['save']]);
        $this->middleware('permission:achievement-summary-update', ['only' => ['onUpdateStatus']]);
        $this->middleware('permission:achievement-summary-delete', ['only' => ['onDelete', 'onDestroy']]);
        $this->middleware('permission:achievement-summary-restore', ['only' => ['onRestore']]);
    }

    public function index()
    {
        return view('admin::pages.page.achievement-summary.index');
    }

    public function data()
    {
        $data = ListOfValue::query()
            ->where('type', $this->type)
            ->when(filled(request('search')), function ($q) {
                $search = '%' . request('search') . '%';
                $q->where(function ($query) use ($search) {
                    $query->where('code', 'like', $search)
                        ->orWhere('title->en', 'like', $search)
                        ->orWhere('title->km', 'like', $search)
                        ->orWhere('description->en', 'like', $search)
                        ->orWhere('description->km', 'like', $search);
                });
            })
            ->when(request('trash'), function ($q) {
                $q->onlyTrashed();
            })
            ->orderByDesc('sequence')
            ->paginate(25);

        return response()->json(['data' => $data]);
    }

    public function save(AchievementSummaryRequest $request)
    {
        $permission = $request->id ? 'achievement-summary-update' : 'achievement-summary-create';
        abort_unless(Auth::guard('admin')->user()?->can($permission), 403);

        DB::beginTransaction();
        try {
            $image = UploadFile::uploadFile('/list-of-value', $request->file('image'), $request->tmp_file);
            $input = [
                'code' => $request->number,
                'type' => $this->type,
                'title' => [
                    'en' => $request->title_en,
                    'km' => $request->title_km,
                ],
                'description' => [
                    'en' => $request->description_en,
                    'km' => $request->description_km,
                ],
                'sequence' => $request->sequence,
                'status' => $request->status,
                'image' => $image,
                'user_id' => Auth::guard('admin')->id(),
            ];

            if (!$request->id) {
                ListOfValue::create($input);
            } else {
                $data = ListOfValue::where('type', $this->type)->findOrFail($request->id);
                if ($request->file('image') || !$request->tmp_file) {
                    UploadFile::deleteFile('/list-of-value', $data->image);
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
            UploadFile::deleteFile('/list-of-value', $data->image);
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
