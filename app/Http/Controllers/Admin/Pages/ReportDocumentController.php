<?php

namespace App\Http\Controllers\Admin\Pages;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Page\ReportDocumentRequest;
use App\Models\ListOfValue;
use App\Models\UploadFile;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportDocumentController extends Controller
{
    protected string $type;
    protected string $categoryType;

    public function __construct()
    {
        parent::__construct();

        $this->type = config('dummy.module.report_document.key');
        $this->categoryType = config('dummy.module.report_document_category.key');

        $this->middleware('permission:report-document-view', ['only' => ['index', 'data']]);
        $this->middleware('permission:report-document-create|report-document-update', ['only' => ['save']]);
        $this->middleware('permission:report-document-update', ['only' => ['onUpdateStatus']]);
        $this->middleware('permission:report-document-delete', ['only' => ['onDelete', 'onDestroy']]);
        $this->middleware('permission:report-document-restore', ['only' => ['onRestore']]);
    }

    public function index()
    {
        $categories = ListOfValue::query()
            ->where('type', $this->categoryType)
            ->where('status', config('dummy.status.active.key'))
            ->orderBy('sequence')
            ->get(['id', 'title']);

        return view('admin::pages.page.report-document.index', [
            'categories' => $categories,
        ]);
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
                        ->orWhere('add_on->date', 'like', $search);
                });
            })
            ->when(request('trash'), function ($q) {
                $q->onlyTrashed();
            })
            ->orderBy('sequence')
            ->orderByDesc('created_at')
            ->paginate(25);

        $categoryIds = $data->getCollection()
            ->pluck('add_on.category_id')
            ->filter()
            ->unique()
            ->values()
            ->all();

        $categoryMap = ListOfValue::query()
            ->where('type', $this->categoryType)
            ->whereIn('id', $categoryIds)
            ->get(['id', 'title'])
            ->keyBy('id');

        $data->getCollection()->transform(function ($item) use ($categoryMap) {
            $categoryId = data_get($item, 'add_on.category_id');
            $file = data_get($item, 'add_on.file');
            $item->category = $categoryId ? $categoryMap->get($categoryId) : null;
            $item->file_url = $file ? asset('storage/report-document/' . $file) : null;
            return $item;
        });

        return response()->json(['data' => $data]);
    }

    public function save(ReportDocumentRequest $request)
    {
        $permission = $request->id ? 'report-document-update' : 'report-document-create';
        abort_unless(Auth::guard('admin')->user()?->can($permission), 403);

        DB::beginTransaction();
        try {
            $file = UploadFile::uploadFile('/report-document', $request->file('file'), $request->tmp_file);
            $input = [
                'type' => $this->type,
                'title' => [
                    'en' => $request->title_en,
                    'km' => $request->title_km,
                ],
                'add_on' => [
                    'category_id' => (int) $request->category_id,
                    'date' => Carbon::createFromFormat('d/m/Y', $request->date)->format('Y-m-d'),
                    'file' => $file,
                ],
                'sequence' => $request->sequence,
                'status' => $request->status,
                'user_id' => Auth::guard('admin')->id(),
            ];

            if (!$request->id) {
                ListOfValue::create($input);
            } else {
                $data = ListOfValue::where('type', $this->type)->findOrFail($request->id);
                if ($request->file('file') || !$request->tmp_file) {
                    UploadFile::deleteFile('/report-document', data_get($data, 'add_on.file'));
                }
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
            UploadFile::deleteFile('/report-document', data_get($data, 'add_on.file'));
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
