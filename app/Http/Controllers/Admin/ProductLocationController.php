<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductLocationRequest;
use App\Models\ProductLocation;
use Exception;
use Illuminate\Support\Facades\DB;

class ProductLocationController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:product-location-view',   ['only' => ['index', 'detail', 'data']]);
        $this->middleware('permission:product-location-create', ['only' => ['save']]);
        $this->middleware('permission:product-location-update', ['only' => ['save', 'updateStatus']]);
        $this->middleware('permission:product-location-delete', ['only' => ['delete']]);
    }

    public function index()
    {
        return view('admin::pages.product-location.index');
    }

    public function data()
    {
        try {
            $pag = request('pag') ?? 50;
            $data = ProductLocation::query()
                ->when(request('status'), fn($q) => $q->where('is_active', request('status') === $this->active))
                ->when(request('search'), function ($q) {
                    $q->where(function ($query) {
                        $query->where('name_en', 'LIKE', '%' . request('search') . '%')
                            ->orWhere('location_type', 'LIKE', '%' . request('search') . '%')
                            ->orWhere('address', 'LIKE', '%' . request('search') . '%');
                    });
                })
                ->orderByDesc('created_at')
                ->paginate($pag);
            return $data;
        } catch (Exception $e) {
            return $this->responseError();
        }
    }

    public function detail()
    {
        try {
            return $this->responseSuccess(
                ProductLocation::findOrFail(request('id'))
            );
        } catch (Exception $e) {
            return $this->responseError();
        }
    }

    public function save(ProductLocationRequest $request)
    {
        DB::beginTransaction();
        try {
            $payload = [
                'name_en'       => $request->name_en,
                'location_type' => $request->location_type,
                'address'       => $request->address,
                'is_active'     => $request->status === $this->active,
            ];

            if ($request->id) {
                ProductLocation::findOrFail($request->id)->update($payload);
            } else {
                ProductLocation::create($payload);
            }

            DB::commit();
            return $this->responseSuccess();
        } catch (Exception $e) {
            DB::rollBack();
            return $this->responseError($e->getMessage());
        }
    }

    public function updateStatus()
    {
        DB::beginTransaction();
        try {
            ProductLocation::findOrFail(request('id'))->update([
                'is_active' => request('status') === $this->active,
            ]);
            DB::commit();
            return $this->responseSuccess(null, __('form.message.update.success'));
        } catch (Exception $e) {
            DB::rollBack();
            return $this->responseError();
        }
    }

    public function delete()
    {
        DB::beginTransaction();
        try {
            ProductLocation::findOrFail(request('id'))->delete();
            DB::commit();
            return $this->responseSuccess(null, __('form.message.delete.success'));
        } catch (Exception $e) {
            DB::rollBack();
            return $this->responseError();
        }
    }
}
