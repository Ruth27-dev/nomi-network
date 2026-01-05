<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BankAccountRequest;
use App\Models\BankAccount;
use App\Models\UploadFile;
use App\Services\ActivityLogService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BankAccountController extends Controller
{
    protected $module;
    public function __construct()
    {
        parent::__construct();
        $this->module = [
            'key'   => config('dummy.module.bank_account.key'),
        ];
        $this->middleware('permission:bank-account-view', ['only' => ['index', 'data']]);
        $this->middleware('permission:bank-account-create', ['only' => ['onCreate', 'onSave']]);
        $this->middleware('permission:bank-account-update', ['only' => ['onUpdateStatus']]);
        $this->middleware('permission:bank-account-delete', ['only' => ['onDelete']]);
        $this->middleware('permission:bank-account-restore', ['only' => ['onRestore']]);
    }

    public function index()
    {
        return view("admin::pages.setting.bank-account.index");
    }

    public function data()
    {
        $data = BankAccount::query()
            ->when(filled(request('search')), function ($q) {
                $q->where('bank_name', 'like', '%' . request('search') . '%');
                $q->orWhere('bank_number', 'like', '%' . request('search') . '%');
                $q->orWhere('account_name', 'like', '%' . request('search') . '%');
                $q->orWhereHas('branch', function ($qq) {
                    $qq->where('title->en', 'LIKE', '%' . request('search') . '%');
                    $qq->orWhere('title->km', 'LIKE', '%' . request('search') . '%');
                });
            })->when(request('trash'), function ($q) {
                $q->onlyTrashed();
            })
            ->orderByDesc("created_at")
            ->paginate(25);

        return response()->json(['data' => $data]);
    }

    public function save(BankAccountRequest $request)
    {
        DB::beginTransaction();
        try {
            $qr_code = UploadFile::uploadFile('/bank-account', $request->file('qr_code'));
            $input = [
                'branch_id'     => $request->branch_id,
                'bank_name'     => $request->bank_name,
                'bank_number'   => $request->bank_number,
                'account_name'  => $request->account_name,
                'ordering'      => $request->ordering,
                'qr_code'       => $qr_code,
                'status'        => $request->status,
                'user_id'       => Auth::user()->id,

            ];

            if (!$request->id) {
                $data = BankAccount::create($input);
                $original_data = $data->getOriginal();
            } else {
                $data = BankAccount::find($request->id);
                if ($request->file('qr_code') || !$request->tmp_file) {
                    UploadFile::deleteFile('/bank-account', $data->qr_code);
                }
                $input['qr_code'] = $qr_code ?? $request->tmp_file;
                $original_data = $data->getOriginal();
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
            dd($e);
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
            $user = BankAccount::find($request->id);
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
            BankAccount::find($request->id)->delete();
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
            BankAccount::onlyTrashed()->find($request->id)->restore();
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
            BankAccount::onlyTrashed()->find($request->id)->forceDelete();
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
            $data['max_ordering'] = BankAccount::max('ordering') + 1;
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}