<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductAttributeRequest;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use Exception;
use Illuminate\Support\Facades\DB;

class ProductAttributeController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:product-attribute-view', ['only' => ['index', 'detail', 'data']]);
        $this->middleware('permission:product-attribute-create', ['only' => ['save']]);
        $this->middleware('permission:product-attribute-update', ['only' => ['save', 'updateStatus']]);
        $this->middleware('permission:product-attribute-delete', ['only' => ['delete']]);
    }

    public function index()
    {
        return view('admin::pages.product-attribute.index');
    }

    public function data()
    {
        try {
            $pag = request('pag') ?? 50;
            $data = ProductAttribute::with('values')
                ->when(request('status'), fn($q) => $q->where('is_active', request('status') === $this->active))
                ->when(request('search'), function ($q) {
                    $q->where(function ($query) {
                        $query->where('name', 'LIKE', '%' . request('search') . '%')
                            ->orWhere('code', 'LIKE', '%' . request('search') . '%')
                            ->orWhere('input_type', 'LIKE', '%' . request('search') . '%');
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
                ProductAttribute::with('values')->findOrFail(request('id'))
            );
        } catch (Exception $e) {
            return $this->responseError();
        }
    }

    public function save(ProductAttributeRequest $request)
    {
        DB::beginTransaction();
        try {
            $payload = [
                'name' => $request->name,
                'code' => $request->sku,
                'input_type' => $request->input_type,
                'is_variation' => (bool) ($request->is_variation ?? true),
                'is_active' => $request->status === $this->active,
            ];

            if ($request->id) {
                $attribute = ProductAttribute::findOrFail($request->id);
                $attribute->update($payload);
            } else {
                $attribute = ProductAttribute::create($payload);
            }

            $this->syncValues($attribute->id, $request->values ?? []);

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
            ProductAttribute::findOrFail(request('id'))->update([
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
            ProductAttribute::findOrFail(request('id'))->delete();
            DB::commit();
            return $this->responseSuccess(null, __('form.message.delete.success'));
        } catch (Exception $e) {
            DB::rollBack();
            return $this->responseError();
        }
    }

    private function syncValues(int $attributeId, array $values): void
    {
        ProductAttributeValue::where('product_attribute_id', $attributeId)->delete();

        $rows = [];
        foreach ($values as $index => $value) {
            if (!filled($value)) {
                continue;
            }
            $rows[] = [
                'product_attribute_id' => $attributeId,
                'value' => $value,
                'code' => $value,
                'sort_order' => $index + 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($rows)) {
            ProductAttributeValue::insert($rows);
        }
    }
}
