<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ShippingMethodRequest;
use App\Models\ListOfValue;
use App\Models\UploadFile;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShippingMethodController extends Controller
{
    protected string $type;

    public function __construct()
    {
        parent::__construct();
        $this->type = config('dummy.module.shipping_method.key');

        $this->middleware('permission:shipping-method-view', ['only' => ['index', 'data']]);
        $this->middleware('permission:shipping-method-create|shipping-method-update', ['only' => ['save']]);
        $this->middleware('permission:shipping-method-update', ['only' => ['onUpdateStatus']]);
        $this->middleware('permission:shipping-method-delete', ['only' => ['onDelete', 'onDestroy']]);
        $this->middleware('permission:shipping-method-restore', ['only' => ['onRestore']]);
    }

    public function index()
    {
        return view('admin::pages.setting.shipping-method.index');
    }

    public function data()
    {
        $data = ListOfValue::query()
            ->where('type', $this->type)
            ->when(filled(request('status')), function ($q) {
                $q->where('status', request('status'));
            })
            ->when(filled(request('search')), function ($q) {
                $search = '%' . request('search') . '%';
                $q->where(function ($query) use ($search) {
                    $query->where('title->en', 'like', $search)
                        ->orWhere('title->km', 'like', $search)
                        ->orWhere('add_on->price', 'like', $search);
                });
            })
            ->when(filled(request('trash')), function ($q) {
                $q->onlyTrashed();
            })
            ->orderBy('sequence')
            ->orderByDesc('created_at')
            ->paginate(25);

        return response()->json(['data' => $data]);
    }

    public function save(ShippingMethodRequest $request)
    {
        $permission = $request->id ? 'shipping-method-update' : 'shipping-method-create';
        abort_unless(Auth::guard('admin')->user()?->can($permission), 403);

        DB::beginTransaction();
        try {
            $image = UploadFile::uploadFile('/list-of-value', $request->file('image'), $request->tmp_file);
            $input = [
                'type' => $this->type,
                'title' => [
                    'en' => $request->title_en,
                    'km' => $request->title_km,
                ],
                'add_on' => [
                    'price' => $request->price,
                ],
                'sequence' => $request->ordering,
                'image' => $image,
                'status' => $request->status,
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
