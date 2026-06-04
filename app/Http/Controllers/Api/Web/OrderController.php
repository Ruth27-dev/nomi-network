<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Models\ListOfValue;
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
            'shipping_method_id' => 'required|exists:list_of_values,id',
            'discount_amount' => 'nullable|numeric|min:0',
            'user_address_id' => 'nullable|exists:user_addresses,id',
            'recipient_name' => 'nullable|string|max:255',
            'recipient_phone' => 'nullable|string|max:50',
            'address_line' => 'nullable|string',
            'province' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $shippingMethod = ListOfValue::findOrFail($request->shipping_method_id);
            $shippingFee    = (float) data_get($shippingMethod->add_on, 'price', 0);
            $shippingTitle  = is_array($shippingMethod->title)
                ? ($shippingMethod->title[app()->getLocale()] ?? $shippingMethod->title['en'] ?? collect($shippingMethod->title)->first() ?? '')
                : (string) $shippingMethod->title;

            $address = null;
            $manualAddress = null;

            if ($request->user_address_id) {
                $address = UserAddress::where('user_id', $user->id)->findOrFail($request->user_address_id);
            } else {
                $address = UserAddress::where('user_id', $user->id)->where('is_default', true)->first();
            }

            if (!$address && $request->filled('recipient_name') && $request->filled('recipient_phone') && $request->filled('address_line')) {
                $manualAddress = [
                    'recipient_name' => $request->recipient_name,
                    'recipient_phone' => $request->recipient_phone,
                    'shipping_address' => trim(implode(', ', array_filter([
                        $request->address_line,
                        $request->district,
                        $request->city,
                        $request->province,
                        $request->postal_code,
                    ]))),
                ];
            }

            $order = Order::create([
                'order_no' => 'ORD-' . now()->format('YmdHis') . '-' . strtoupper(substr(md5(uniqid('', true)), 0, 6)),
                'user_id' => $user->id,
                'user_address_id' => $address?->id,
                'shipping_method_id'    => $shippingMethod->id,
                'shipping_method_title' => $shippingTitle,
                'recipient_name' => $address ? $address->recipient_name : ($manualAddress['recipient_name'] ?? null),
                'recipient_phone' => $address ? $address->recipient_phone : ($manualAddress['recipient_phone'] ?? null),
                'shipping_address' => $address ? trim(implode(', ', array_filter([
                    $address->address_line,
                    $address->district,
                    $address->city,
                    $address->province,
                    $address->postal_code,
                ]))) : ($manualAddress['shipping_address'] ?? null),
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

            $discount = (float) ($request->discount_amount ?? 0);
            $grandTotal = max(0, $subTotal + $shippingFee - $discount);

            $order->update([
                'sub_total' => $subTotal,
                'shipping_fee' => $shippingFee,
                'discount_amount' => $discount,
                'grand_total' => $grandTotal,
            ]);

            DB::commit();
            return $this->responseSuccess($this->formatOrderWithTracking($order->load('items')), 'Order created successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return $this->responseError($e->getMessage());
        }
    }

    public function orders()
    {
        try {
            $user = Auth::guard('api_web')->user();
            $pag  = request('per_page', 20);
            $data = Order::query()
                ->with('items')
                ->withCount('items')
                ->where('user_id', $user->id)
                ->orderByDesc('id')
                ->paginate($pag);

            $data->getCollection()->transform(fn($order) => $this->formatOrderWithTracking($order));

            return $this->responseSuccess($data);
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }
    }

    public function detail()
    {
        try {
            $user  = Auth::guard('api_web')->user();
            $order = Order::query()
                ->with('items')
                ->where('user_id', $user->id)
                ->findOrFail(request('id'));

            return $this->responseSuccess($this->formatOrderWithTracking($order));
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }
    }

    public function track()
    {
        try {
            $tranId = request('tran_id');
            if (!$tranId) {
                return response()->json(['message' => 'tran_id is required'], 422);
            }

            $order = Order::query()
                ->with('items')
                ->where('order_no', $tranId)
                ->first();

            if (!$order) {
                return response()->json(['message' => 'Order not found.'], 404);
            }

            return $this->responseSuccess($this->formatOrderWithTracking($order));
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }
    }

    private function formatOrderWithTracking(Order $order): array
    {
        $steps = ['pending', 'confirmed', 'shipping', 'completed'];
        $currentIndex = array_search($order->status, $steps);

        $tracking = collect($steps)->map(function ($step, $i) use ($currentIndex, $order) {
            $done = $currentIndex !== false && $i <= $currentIndex;
            return [
                'status' => $step,
                'label'  => ucfirst($step),
                'done'   => $done,
                'active' => $step === $order->status,
            ];
        })->values();

        return [
            'id'                    => $order->id,
            'order_no'              => $order->order_no,
            'status'                => $order->status,
            'payment_status'        => $order->payment_status,
            'payment_method'        => $order->payment_method,
            'sub_total'             => (float) $order->sub_total,
            'shipping_fee'          => (float) $order->shipping_fee,
            'discount_amount'       => (float) $order->discount_amount,
            'grand_total'           => (float) $order->grand_total,
            'recipient_name'        => $order->recipient_name,
            'recipient_phone'       => $order->recipient_phone,
            'shipping_address'      => $order->shipping_address,
            'shipping_method_title' => $order->shipping_method_title,
            'note'                  => $order->note,
            'created_at'            => $order->created_at,
            'completed_at'          => $order->completed_at,
            'items_count'           => $order->items_count ?? $order->items->count(),
            'items'                 => $this->formatOrderItems($order),
            'tracking'              => $tracking,
        ];
    }

    private function formatOrderItems(Order $order): array
    {
        return $order->items->map(function (OrderItem $item) {
            $events = collect($item->tracking_events ?? [])
                ->filter(fn($event) => is_array($event))
                ->sortByDesc(fn($event) => $event['tracked_at'] ?? '')
                ->values()
                ->map(function ($event, $index) {
                    $status = $event['status'] ?? null;

                    return [
                        'status' => $status,
                        'label' => $this->trackingStatusLabel($status),
                        'description' => $event['description'] ?? null,
                        'location' => $event['location'] ?? null,
                        'tracked_at' => $event['tracked_at'] ?? null,
                        'active' => $index === 0,
                        'done' => true,
                    ];
                });
            $latestEvent = $events->first();
            $eventList = $events->values()->all();

            return [
                'id' => $item->id,
                'order_id' => $item->order_id,
                'product_id' => $item->product_id,
                'product_variation_id' => $item->product_variation_id,
                'product_name' => $item->product_name,
                'product_sku' => $item->product_sku,
                'variation_name' => $item->variation_name,
                'variation_sku' => $item->variation_sku,
                'quantity' => (int) $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'line_total' => (float) $item->line_total,
                'shipping_carrier' => $item->shipping_carrier,
                'tracking_number' => $item->tracking_number,
                'tracking_status' => $latestEvent['status'] ?? null,
                'tracking_label' => $latestEvent['label'] ?? null,
                'tracking_events' => $eventList,
                'shipping_tracking' => [
                    'carrier' => $item->shipping_carrier,
                    'tracking_number' => $item->tracking_number,
                    'current_status' => $latestEvent['status'] ?? null,
                    'current_label' => $latestEvent['label'] ?? null,
                    'events' => $eventList,
                ],
            ];
        })->values()->all();
    }

    private function trackingStatusLabel(?string $status): ?string
    {
        return [
            'created' => 'Created',
            'picked_up' => 'Picked Up',
            'in_transit' => 'In Transit',
            'out_for_delivery' => 'Out for Delivery',
            'delivered' => 'Delivered',
            'failed' => 'Delivery Failed',
            'returned' => 'Returned',
        ][$status] ?? $status;
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
