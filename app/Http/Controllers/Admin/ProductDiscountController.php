<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DiscountRequest;
use App\Models\Discount;
use App\Models\DiscountCondition;
use App\Models\UploadFile;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductDiscountController extends Controller
{
    protected $module;

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:product-discount-view', ['only' => ['index', 'detail', 'data']]);
        $this->middleware('permission:product-discount-create', ['only' => ['save']]);
        $this->middleware('permission:product-discount-update', ['only' => ['save']]);
        $this->middleware('permission:product-discount-update-status', ['only' => ['updateStatus']]);
        $this->middleware('permission:product-discount-delete', ['only' => ['delete']]);
        $this->middleware('permission:product-discount-restore', ['only' => ['restore']]);

        $this->module = [
            'key' => config('dummy.module.product_discount.key'),
        ];
    }

    public function index()
    {
        return view('admin::pages.product.discount.index');
    }

    public function data()
    {
        try {
            $pag = request('pag') ?? 50;
            $data = Discount::query()
                ->when(request('status'), fn($q) => $q->where('status', request('status')))
                ->when(request('trash'), fn($q) => $q->onlyTrashed())
                ->when(request('search'), function ($q) {
                    $q->where(function ($q) {
                        $q->where('code', 'LIKE', '%' . request('search') . '%');
                        $q->orWhere('title->en', 'LIKE', '%' . request('search') . '%');
                        $q->orWhere('title->km', 'LIKE', '%' . request('search') . '%');
                        $q->orWhere('remark->en', 'LIKE', '%' . request('search') . '%');
                        $q->orWhere('remark->km', 'LIKE', '%' . request('search') . '%');
                    });
                })
                ->orderByDesc('created_at')
                ->paginate($pag);

            return $data;
        } catch (Exception $e) {
            return $this->responseError();
        }
    }

    public function save(DiscountRequest $request)
    {
        DB::beginTransaction();
        try {
            $image = UploadFile::uploadFile('/discount', $request->file('image'), $request->tmp_file);
            $payload = [
                'type'              => $request->type,
                'code'              => $request->code,
                'status'            => $request->status,
                'title'             => [
                    'en' => $request->title_en,
                    'km' => $request->title_km,
                ],
                'discount_amount'   => $request->discount_amount,
                'discount_type'     => $request->discount_type,
                'remark'            => [
                    'en' => $request->remark_en,
                    'km' => $request->remark_km,
                ],
                'start_date'        => $request->start_date ? Carbon::createFromFormat('d/m/Y', $request->start_date) : null,
                'end_date'          => $request->end_date ? Carbon::createFromFormat('d/m/Y', $request->end_date) : null,
                'usage_limit'       => $request->usage_limit,
                'usage_per_customer'=> $request->usage_per_customer,
                'is_flat_discount'  => $request->is_flat_discount,
                'image'             => $image,
                'user_id'           => Auth::id(),
            ];

            if (!$request->id) {
                $discount = Discount::create($payload);
                if (filled($request->product_ids)) {
                    $discount->products()->sync($request->product_ids ?? []);
                }
                $this->syncConditions($discount, $request);
            } else {
                $discount = Discount::findOrFail($request->id);
                if ($request->file('image')) {
                    UploadFile::deleteFile('/discount', $discount?->image);
                }
                $discount->products()->sync($request->product_ids ?? []);
                $discount->update($payload);
                $this->syncConditions($discount, $request);
            }

            DB::commit();
            return $this->responseSuccess();
        } catch (Exception $e) {
            DB::rollBack();
            return $this->responseError();
        }
    }

    private function syncConditions(Discount $discount, DiscountRequest $request): void
    {
        $conditions = [
            config('dummy.discount.condition.min') => $request->min_amount,
            config('dummy.discount.condition.max') => $request->max_amount,
        ];

        foreach ($conditions as $type => $amount) {
            if (!filled($amount)) {
                continue;
            }

            $existing = $discount->conditions()->where('type', $type)->first();
            if ($existing) {
                $existing->update(['amount' => $amount]);
            } else {
                DiscountCondition::create([
                    'discount_id' => $discount->id,
                    'type'        => $type,
                    'amount'      => $amount,
                ]);
            }
        }
    }

    public function detail()
    {
        try {
            $data = Discount::with(['conditions', 'products.product'])->findOrFail(request('id'));
            return $this->responseSuccess($data);
        } catch (Exception $e) {
            return $this->responseError();
        }
    }

    public function updateStatus()
    {
        try {
            $data = Discount::findOrFail(request('id'));
            $data->update([
                'status' => request('status'),
            ]);
            return $this->responseSuccess(null, __('form.message.update.success'));
        } catch (Exception $e) {
            return $this->responseError();
        }
    }

    public function delete()
    {
        try {
            $data = Discount::findOrFail(request('id'));
            $data->delete();
            return $this->responseSuccess(null, __('form.message.delete.success'));
        } catch (Exception $e) {
            return $this->responseError();
        }
    }

    public function restore()
    {
        try {
            $data = Discount::withTrashed()->findOrFail(request('id'));
            $data->restore();
            return $this->responseSuccess(null, __('form.message.restore.success'));
        } catch (Exception $e) {
            return $this->responseError();
        }
    }

    public function destroy()
    {
        try {
            $data = Discount::withTrashed()->findOrFail(request('id'));
            if ($data->image) {
                UploadFile::deleteFile('/discount', $data->image);
            }
            $data->products()->detach();
            $data->conditions()->delete();
            $data->forceDelete();
            return $this->responseSuccess(null, __('form.message.delete.success'));
        } catch (Exception $e) {
            return $this->responseError();
        }
    }
}
