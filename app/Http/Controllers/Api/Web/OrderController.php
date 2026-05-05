<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\ProductVariation;
use App\Models\UserAddress;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function addressList()
    {
        $user = Auth::guard('api_web')->user();
        $data = UserAddress::query()
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->orderByDesc('is_default')
            ->orderByDesc('id')
            ->get();

        return $this->responseSuccess($data);
    }

    public function saveAddress(Request $request)
    {
        $user = Auth::guard('api_web')->user();
        $validator = Validator::make($request->all(), [
            'recipient_name' => 'required|string|max:255',
            'recipient_phone' => 'required|string|max:50',
            'address_line' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            if ($request->boolean('is_default')) {
                UserAddress::where('user_id', $user->id)->update(['is_default' => false]);
            }

            $payload = [
                'user_id' => $user->id,
                'label' => $request->label,
                'recipient_name' => $request->recipient_name,
                'recipient_phone' => $request->recipient_phone,
                'address_line' => $request->address_line,
                'province' => $request->province,
                'city' => $request->city,
                'district' => $request->district,
                'postal_code' => $request->postal_code,
                'is_default' => $request->boolean('is_default'),
                'is_active' => true,
            ];

            if ($request->id) {
                $address = UserAddress::where('user_id', $user->id)->findOrFail($request->id);
                $address->update($payload);
            } else {
                $address = UserAddress::create($payload);
            }

            DB::commit();
            return $this->responseSuccess($address, 'Address saved successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return $this->responseError($e->getMessage());
        }
    }

    public function create(Request $request)
    {
        $user = Auth::guard('api_web')->user();
        $validator = Validator::make($request->all(), [
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.product_variation_id' => 'nullable|exists:product_variations,id',
            'items.*.quantity' => 'required|integer|min:1',
            'shipping_fee' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'user_address_id' => 'nullable|exists:user_addresses,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $address = null;
            if ($request->user_address_id) {
                $address = UserAddress::where('user_id', $user->id)->findOrFail($request->user_address_id);
            } else {
                $address = UserAddress::where('user_id', $user->id)->where('is_default', true)->first();
            }

            $order = Order::create([
                'order_no' => 'ORD-' . now()->format('YmdHis') . '-' . strtoupper(substr(md5(uniqid('', true)), 0, 6)),
                'user_id' => $user->id,
                'user_address_id' => $address?->id,
                'shipping_method_id' => $request->shipping_method_id,
                'shipping_method_title' => $request->shipping_method_title,
                'recipient_name' => $address?->recipient_name,
                'recipient_phone' => $address?->recipient_phone,
                'shipping_address' => $address ? trim(implode(', ', array_filter([
                    $address->address_line,
                    $address->district,
                    $address->city,
                    $address->province,
                    $address->postal_code,
                ]))) : null,
                'note' => $request->note,
                'payment_method' => $request->payment_method ?? 'cod',
                'payment_status' => 'unpaid',
                'status' => 'pending',
            ]);

            $subTotal = 0;
            foreach ($request->items as $row) {
                $product = Product::findOrFail($row['product_id']);
                $variation = null;
                if (!empty($row['product_variation_id'])) {
                    $variation = ProductVariation::where('product_id', $product->id)->findOrFail($row['product_variation_id']);
                }

                $qty = (int) $row['quantity'];
                $unitPrice = $variation ? (float) $variation->price : (float) $product->price;
                $lineTotal = $unitPrice * $qty;

                $stock = ProductStock::query()
                    ->where('product_id', $product->id)
                    ->where(function ($q) use ($variation) {
                        if ($variation) {
                            $q->where('product_variation_id', $variation->id);
                        } else {
                            $q->whereNull('product_variation_id');
                        }
                    })
                    ->lockForUpdate()
                    ->first();

                if (!$stock) {
                    throw new Exception("Stock record not found for {$product->name_en}");
                }

                if ((int) $stock->stock_available < $qty) {
                    throw new Exception("Insufficient stock for {$product->name_en}");
                }

                $stock->stock_reserved = (int) $stock->stock_reserved + $qty;
                $stock->stock_available = (int) $stock->stock_on_hand - (int) $stock->stock_reserved;
                $stock->save();

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_variation_id' => $variation?->id,
                    'product_name' => $product->name_en,
                    'product_sku' => $product->sku,
                    'variation_name' => $variation?->name,
                    'variation_sku' => $variation?->sku,
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'line_total' => $lineTotal,
                ]);

                $subTotal += $lineTotal;
            }

            $shippingFee = (float) ($request->shipping_fee ?? 0);
            $discount = (float) ($request->discount_amount ?? 0);
            $grandTotal = max(0, $subTotal + $shippingFee - $discount);

            $order->update([
                'sub_total' => $subTotal,
                'shipping_fee' => $shippingFee,
                'discount_amount' => $discount,
                'grand_total' => $grandTotal,
            ]);

            DB::commit();
            return $this->responseSuccess($order->load('items'), 'Order created successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return $this->responseError($e->getMessage());
        }
    }

    public function orders()
    {
        try {
            $user = Auth::guard('api_web')->user();
            $pag = request('per_page', 20);
            $data = Order::query()
                ->withCount('items')
                ->where('user_id', $user->id)
                ->orderByDesc('id')
                ->paginate($pag);

            return $this->responseSuccess($data);
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }
    }

    public function detail()
    {
        try {
            $user = Auth::guard('api_web')->user();
            $order = Order::query()
                ->with('items')
                ->where('user_id', $user->id)
                ->findOrFail(request('id'));
            return $this->responseSuccess($order);
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }
    }

    public function cancel()
    {
        DB::beginTransaction();
        try {
            $user = Auth::guard('api_web')->user();
            $order = Order::query()->with('items')->where('user_id', $user->id)->findOrFail(request('id'));

            if (!in_array($order->status, ['pending', 'confirmed'], true)) {
                return $this->responseError('Only pending/confirmed orders can be cancelled.');
            }

            foreach ($order->items as $item) {
                $stock = ProductStock::query()
                    ->where('product_id', $item->product_id)
                    ->where(function ($q) use ($item) {
                        if ($item->product_variation_id) {
                            $q->where('product_variation_id', $item->product_variation_id);
                        } else {
                            $q->whereNull('product_variation_id');
                        }
                    })
                    ->lockForUpdate()
                    ->first();

                if (!$stock) {
                    continue;
                }

                $stock->stock_reserved = max(0, (int) $stock->stock_reserved - (int) $item->quantity);
                $stock->stock_available = (int) $stock->stock_on_hand - (int) $stock->stock_reserved;
                $stock->save();
            }

            $order->update(['status' => 'cancelled']);

            DB::commit();
            return $this->responseSuccess(null, 'Order cancelled successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return $this->responseError($e->getMessage());
        }
    }
}
