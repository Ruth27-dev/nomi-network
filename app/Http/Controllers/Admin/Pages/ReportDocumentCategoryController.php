<?php

namespace App\Http\Controllers\Admin\Pages;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Page\ReportDocumentCategoryRequest;
use App\Models\ListOfValue;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportDocumentCategoryController extends Controller
{
    protected string $type;

    public function __construct()
    {
        parent::__construct();

        $this->type = config('dummy.module.report_document_category.key');

        $this->middleware('permission:report-document-category-view', ['only' => ['index', 'data']]);
        $this->middleware('permission:report-document-category-create|report-document-category-update', ['only' => ['save']]);
        $this->middleware('permission:report-document-category-update', ['only' => ['onUpdateStatus']]);
        $this->middleware('permission:report-document-category-delete', ['only' => ['onDelete', 'onDestroy']]);
        $this->middleware('permission:report-document-category-restore', ['only' => ['onRestore']]);
    }

    public function index()
    {
        return view('admin::pages.page.report-document-category.index');
    }

    public function data()
    {
        $data = ListOfValue::query()
            ->where('type', $this->type)
            ->when(filled(request('search')), function ($q) {
                $search = '%' . request('search') . '%';
                $q->where(function ($query) use ($search) {
                    $query->where('title->en', 'like', $search)
                        ->orWhere('title->km', 'like', $search);
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

    public function save(ReportDocumentCategoryRequest $request)
    {
        $permission = $request->id ? 'report-document-category-update' : 'report-document-category-create';
        abort_unless(Auth::guard('admin')->user()?->can($permission), 403);

        DB::beginTransaction();
        try {
            $input = [
                'type' => $this->type,
                'title' => [
                    'en' => $request->title_en,
                    'km' => $request->title_km,
                ],
                'sequence' => $request->sequence,
                'status' => $request->status,
                'user_id' => Auth::guard('admin')->id(),
            ];

            if (!$request->id) {
                ListOfValue::create($input);
            } else {
                ListOfValue::where('type', $this->type)->findOrFail($request->id)->update($input);
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
            ListOfValue::onlyTrashed()->where('type', $this->type)->findOrFail($request->id)->forceDelete();

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

