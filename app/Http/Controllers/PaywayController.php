<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\ListOfValue;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaywayTransaction;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\ProductVariation;
use App\Models\UserAddress;
use App\Models\UserCartItem;
use App\Services\PayWayService;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaywayController extends Controller
{
    private PayWayService $payWay;
    private StockService  $stock;

    public function __construct(PayWayService $payWay, StockService $stock)
    {
        $this->payWay = $payWay;
        $this->stock  = $stock;
    }

    public function index()
    {
        return view('pages.payway.viewData');
    }

    /**
     * POST /api/web/create-payment
     *
     * For order (requires auth, pass items array):
     *   { "items": [...], "shipping_method_id", "firstname", "lastname", "phone", "payment_option",
     *     "user_address_id"?, "recipient_name"?, "recipient_phone"?, "address_line"?,
     *     "province"?, "city"?, "district"?, "postal_code"?, "discount_amount"?, "note"? }
     *   Order is created only after payment succeeds.
     *
     * For donation (no items, no auth required):
     *   { "amount", "firstname", "lastname", "phone", "payment_option", "donation_type"?, "note"? }
     */
    public function payway_form(Request $request)
    {
        $input         = $request->all();
        $paymentOption = $input['payment_option'] ?? null;

        // Support items sent as a JSON string (e.g. from some mobile clients)
        if (isset($input['items']) && is_string($input['items'])) {
            $decoded = json_decode($input['items'], true);
            if (is_array($decoded)) {
                $input['items'] = $decoded;
            }
        }

        $hasItems = isset($input['items']) && is_array($input['items']) && count($input['items']) > 0;

        $allowedOptions = ['cards', 'abapay_khqr', 'abapay_khqr_deeplink'];
        if (!in_array($paymentOption, $allowedOptions)) {
            return response()->json(['message' => 'payment_option must be one of: ' . implode(', ', $allowedOptions)], 422);
        }

        foreach (['firstname', 'lastname', 'phone', 'payment_option'] as $field) {
            if (!array_key_exists($field, $input)) {
                return response()->json(['message' => "Missing required field: $field"], 422);
            }
        }

        // For orders send "items" (array of {product_id, quantity}).
        // For donations send "amount" (number).
        if (!$hasItems) {
            if (array_key_exists('items', $input)) {
                return response()->json(['message' => 'items must be a non-empty array with product_id and quantity for each entry'], 422);
            }
            if (!array_key_exists('amount', $input)) {
                return response()->json([
                    'message' => 'Invalid request: send "items" (array) for an order payment, or "amount" (number) for a donation.',
                ], 422);
            }
        }

        if ($hasItems && !array_key_exists('shipping_method_id', $input)) {
            return response()->json(['message' => 'Missing required field: shipping_method_id'], 422);
        }

        DB::beginTransaction();
        try {
            $tran_id       = $this->payWay->generateTranId();
            $firstname     = $input['firstname'];
            $lastname      = $input['lastname'];
            $phone         = $input['phone'];
            $email         = $input['email'] ?? '';
            $cancelUrl     = !empty($input['cancel_url']) && filter_var($input['cancel_url'], FILTER_VALIDATE_URL)
                ? (string) $input['cancel_url']
                : null;

            if ($hasItems) {
                // ── Order flow: create order only after payment succeeds ──
                $user = Auth::guard('api_web')->user();
                if (!$user) {
                    DB::rollBack();
                    return response()->json(['message' => 'Authentication required.'], 401);
                }

                $shippingMethod = ListOfValue::findOrFail($input['shipping_method_id']);
                $shippingFee    = (float) data_get($shippingMethod->add_on, 'price', 0);
                $shippingTitle  = is_array($shippingMethod->title)
                    ? ($shippingMethod->title[app()->getLocale()] ?? $shippingMethod->title['en'] ?? collect($shippingMethod->title)->first() ?? '')
                    : (string) $shippingMethod->title;

                // Resolve address
                $address = null;
                if (!empty($input['user_address_id'])) {
                    $address = UserAddress::where('user_id', $user->id)->findOrFail($input['user_address_id']);
                } else {
                    $address = UserAddress::where('user_id', $user->id)->where('is_default', true)->first();
                }

                $recipientName  = null;
                $recipientPhone = null;
                $shippingAddr   = null;
                $userAddressId  = null;

                if ($address) {
                    $recipientName  = $address->recipient_name;
                    $recipientPhone = $address->recipient_phone;
                    $shippingAddr   = trim(implode(', ', array_filter([
                        $address->address_line,
                        $address->district,
                        $address->city,
                        $address->province,
                        $address->postal_code,
                    ])));
                    $userAddressId  = $address->id;
                } elseif (!empty($input['recipient_name']) && !empty($input['recipient_phone']) && !empty($input['address_line'])) {
                    $recipientName  = $input['recipient_name'];
                    $recipientPhone = $input['recipient_phone'];
                    $shippingAddr   = trim(implode(', ', array_filter([
                        $input['address_line'],
                        $input['district'] ?? null,
                        $input['city'] ?? null,
                        $input['province'] ?? null,
                        $input['postal_code'] ?? null,
                    ])));
                }

                // Resolve items, compute totals, reserve stock
                $pendingItems = [];
                $subTotal     = 0;

                foreach ($input['items'] as $row) {
                    $product   = Product::findOrFail($row['product_id']);
                    $variation = null;
                    if (!empty($row['product_variation_id'])) {
                        $variation = ProductVariation::where('product_id', $product->id)
                            ->findOrFail($row['product_variation_id']);
                    }

                    $qty       = (int) $row['quantity'];
                    $unitPrice = $variation ? (float) $variation->price : (float) $product->price;
                    $lineTotal = $unitPrice * $qty;

                    $stock = ProductStock::query()
                        ->where('product_id', $product->id)
                        ->where(function ($q) use ($variation) {
                            $variation
                                ? $q->where('product_variation_id', $variation->id)
                                : $q->whereNull('product_variation_id');
                        })
                        ->lockForUpdate()
                        ->first();

                    if (!$stock) {
                        throw new \Exception("Stock record not found for {$product->name_en}");
                    }
                    if ((int) $stock->stock_available < $qty) {
                        throw new \Exception("Insufficient stock for {$product->name_en}");
                    }

                    $stock->stock_reserved  = (int) $stock->stock_reserved + $qty;
                    $stock->stock_available = (int) $stock->stock_on_hand - (int) $stock->stock_reserved;
                    $stock->save();

                    $pendingItems[] = [
                        'product_id'           => $product->id,
                        'product_variation_id' => $variation?->id,
                        'product_name'         => $product->name_en,
                        'product_sku'          => $product->sku,
                        'variation_name'       => $variation?->name,
                        'variation_sku'        => $variation?->sku,
                        'quantity'             => $qty,
                        'unit_price'           => $unitPrice,
                        'line_total'           => $lineTotal,
                    ];

                    $subTotal += $lineTotal;
                }

                $discount   = (float) ($input['discount_amount'] ?? 0);
                $grandTotal = max(0, $subTotal + $shippingFee - $discount);

                if ($grandTotal <= 0) {
                    DB::rollBack();
                    return response()->json(['message' => 'Order total must be greater than 0.'], 422);
                }

                $amount = number_format($grandTotal, 2, '.', '');

                PaywayTransaction::updateOrCreate(
                    ['tran_id' => $tran_id],
                    [
                        'order_id'       => null,
                        'donation_id'    => null,
                        'tran_type'      => $paymentOption,
                        'order_type'     => 'order',
                        'is_update'      => null,
                        'status_code'    => null,
                        'payment_status' => 'unpaid',
                        'raw_callback'   => [
                            'pending_order' => [
                                'user_id'               => $user->id,
                                'user_address_id'       => $userAddressId,
                                'shipping_method_id'    => $shippingMethod->id,
                                'shipping_method_title' => $shippingTitle,
                                'recipient_name'        => $recipientName,
                                'recipient_phone'       => $recipientPhone,
                                'shipping_address'      => $shippingAddr,
                                'note'                  => $input['note'] ?? null,
                                'payment_option'        => $paymentOption,
                                'sub_total'             => $subTotal,
                                'shipping_fee'          => $shippingFee,
                                'discount_amount'       => $discount,
                                'grand_total'           => $grandTotal,
                                'items'                 => $pendingItems,
                            ],
                        ],
                    ]
                );
            } else {
                // ── Donation flow ──
                $amount = number_format((float) $input['amount'], 2, '.', '');

                PaywayTransaction::updateOrCreate(
                    ['tran_id' => $tran_id],
                    [
                        'order_id'       => null,
                        'donation_id'    => null,
                        'tran_type'      => $paymentOption,
                        'order_type'     => 'donation',
                        'is_update'      => null,
                        'status_code'    => null,
                        'payment_status' => 'unpaid',
                        'raw_callback'   => [
                            'pending_donation' => [
                                'user_id'        => null,
                                'donation_type'  => $input['donation_type'] ?? 'one_time',
                                'amount'         => $amount,
                                'firstname'      => $firstname,
                                'lastname'       => $lastname,
                                'email'          => $email,
                                'payment_option' => $paymentOption,
                                'note'           => $input['note'] ?? null,
                            ],
                        ],
                    ]
                );
            }

            $successUrl = $hasItems
                ? (string) config('payway.success_url')
                : (string) config('payway.donate_success_url');

            $checkoutPayload = $this->payWay->buildCheckoutPayload(
                $tran_id,
                $amount,
                $firstname,
                $lastname,
                $email,
                $phone,
                $paymentOption,
                $cancelUrl,
                $successUrl
            );

            DB::commit();

            $response = [
                'tran_id' => $tran_id,
                'amount'  => $amount,
                'data'    => $checkoutPayload,
            ];

            if ($hasItems) {
                $response['sub_total']       = round($subTotal, 2);
                $response['shipping_fee']    = round($shippingFee, 2);
                $response['discount_amount'] = round($discount, 2);
                $response['grand_total']     = round($grandTotal, 2);
            }

            return response()->json($response);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function generateTranId()
    {
        return response()->json(['tran_id' => $this->payWay->generateTranId()]);
    }

    public function webhook(Request $req)
    {
        Log::info('[webhook] raw payload', $req->all());

        DB::beginTransaction();
        try {
            [$callback, $tranId, $statusCode] = $this->parseWebhookPayload($req);

            $result = $this->applyPaymentStatus($tranId, $statusCode);
            $this->recordPaywayCallback($tranId, $statusCode, $callback);

            DB::commit();
            Log::info('[webhook] done', ['tran_id' => $tranId, 'status_code' => $statusCode]);

            return $result
                ? response('Success', 200)->header('Content-Type', 'text/plain')
                : response('Failed', 422)->header('Content-Type', 'text/plain');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('[webhook] exception: ' . $e->getMessage(), $req->all());
            return response('Failed', 500)->header('Content-Type', 'text/plain');
        }
    }

    public function checkTransaction(Request $request)
    {
        $tranId = $request->input('tran_id');

        if (!$tranId) {
            return response()->json(['message' => 'tran_id is required'], 422);
        }

        $detail = $this->payWay->checkTransaction($tranId);

        if (!$detail['ok']) {
            return response()->json(['message' => 'Failed to fetch transaction from PayWay', 'detail' => $detail], 502);
        }

        $data       = $detail['data']['data'] ?? null;
        $statusCode = (int) ($data['payment_status_code'] ?? -1);

        DB::beginTransaction();
        try {
            $this->applyPaymentStatus($tranId, $statusCode);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('[checkTransaction] exception: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
        }

        return response()->json([
            'tran_id'     => $tranId,
            'status_code' => $statusCode,
            'detail'      => $detail['data'],
        ]);
    }

    private function applyPaymentStatus(string $tranId, int $statusCode): bool
    {
        // --- Donation ---
        $donation = Donation::where('tran_id', $tranId)->first();
        if ($donation) {
            if ($donation->payment_status === 'paid') {
                Log::info('[applyPaymentStatus] donation already paid, skip', ['tran_id' => $tranId]);
                return true;
            }

            $status = match ($statusCode) {
                0       => 'paid',
                2       => 'pending',
                3       => 'failed',
                4       => 'refunded',
                7       => 'failed',
                default => 'failed',
            };

            $donation->update(['payment_status' => $status]);
            Log::info('[applyPaymentStatus] donation updated', ['tran_id' => $tranId, 'status' => $status]);
            return $statusCode === 0;
        }

        // --- Order ---
        $order = Order::where('order_no', $tranId)->first();
        if ($order) {
            if ($order->payment_status === 'paid') {
                Log::info('[applyPaymentStatus] order already paid, skip', ['tran_id' => $tranId]);
                return true;
            }

            $paymentStatus = match ($statusCode) {
                0       => 'paid',
                2       => 'pending',
                3       => 'failed',
                4       => 'refunded',
                7       => 'failed',
                default => 'failed',
            };

            $orderStatus = match ($statusCode) {
                0       => 'confirmed',
                7       => 'cancelled',
                default => $order->status, // keep existing status for pending/declined
            };

            $order->update([
                'payment_status' => $paymentStatus,
                'status'         => $orderStatus,
            ]);

            Log::info('[applyPaymentStatus] order updated', [
                'order_id'       => $order->id,
                'payment_status' => $paymentStatus,
                'status'         => $orderStatus,
            ]);

            if ($statusCode === 0 && $order->user_id) {
                UserCartItem::where('user_id', $order->user_id)->delete();
                Log::info('[applyPaymentStatus] cart cleared', ['user_id' => $order->user_id]);
            }

            return $statusCode === 0;
        }

        // --- Pending donation not yet inserted into donations table ---
        $paywayTxn = PaywayTransaction::where('tran_id', $tranId)
            ->where('order_type', 'donation')
            ->first();

        if ($paywayTxn) {
            $status = match ($statusCode) {
                0       => 'paid',
                2       => 'pending',
                3       => 'failed',
                4       => 'refunded',
                7       => 'failed',
                default => 'failed',
            };

            $updates = [
                'status_code'    => (string) $statusCode,
                'payment_status' => $status,
            ];

            if ($statusCode === 0) {
                $pending = $paywayTxn->raw_callback['pending_donation'] ?? [];
                $donation = Donation::firstOrCreate(
                    ['tran_id' => $tranId],
                    [
                        'user_id'        => $pending['user_id'] ?? null,
                        'donation_type'  => $pending['donation_type'] ?? 'one_time',
                        'amount'         => $pending['amount'] ?? 0,
                        'firstname'      => $pending['firstname'] ?? '',
                        'lastname'       => $pending['lastname'] ?? '',
                        'email'          => $pending['email'] ?? '',
                        'payment_option' => $pending['payment_option'] ?? $paywayTxn->tran_type,
                        'payment_status' => 'paid',
                        'note'           => $pending['note'] ?? null,
                    ]
                );

                $updates['donation_id'] = $donation->id;
            }

            $paywayTxn->update($updates);
            Log::info('[applyPaymentStatus] pending donation transaction updated', [
                'tran_id' => $tranId,
                'status'  => $status,
            ]);

            return $statusCode === 0;
        }

        // --- Pending order not yet inserted into orders table ---
        $paywayTxn = PaywayTransaction::where('tran_id', $tranId)
            ->where('order_type', 'order')
            ->whereNull('order_id')
            ->first();

        if ($paywayTxn) {
            $paymentStatus = match ($statusCode) {
                0       => 'paid',
                2       => 'pending',
                3       => 'failed',
                4       => 'refunded',
                7       => 'failed',
                default => 'failed',
            };

            $updates = [
                'status_code'    => (string) $statusCode,
                'payment_status' => $paymentStatus,
            ];

            if ($statusCode === 0) {
                $pending = $paywayTxn->raw_callback['pending_order'] ?? [];

                $order = Order::firstOrCreate(
                    ['order_no' => $tranId],
                    [
                        'user_id'               => $pending['user_id'] ?? null,
                        'user_address_id'       => $pending['user_address_id'] ?? null,
                        'shipping_method_id'    => $pending['shipping_method_id'] ?? null,
                        'shipping_method_title' => $pending['shipping_method_title'] ?? null,
                        'recipient_name'        => $pending['recipient_name'] ?? null,
                        'recipient_phone'       => $pending['recipient_phone'] ?? null,
                        'shipping_address'      => $pending['shipping_address'] ?? null,
                        'note'                  => $pending['note'] ?? null,
                        'payment_method'        => $pending['payment_option'] ?? $paywayTxn->tran_type,
                        'payment_status'        => 'paid',
                        'status'                => 'confirmed',
                        'sub_total'             => $pending['sub_total'] ?? 0,
                        'shipping_fee'          => $pending['shipping_fee'] ?? 0,
                        'discount_amount'       => $pending['discount_amount'] ?? 0,
                        'grand_total'           => $pending['grand_total'] ?? 0,
                    ]
                );

                if ($order->wasRecentlyCreated) {
                    foreach ($pending['items'] ?? [] as $item) {
                        OrderItem::create([
                            'order_id'             => $order->id,
                            'product_id'           => $item['product_id'],
                            'product_variation_id' => $item['product_variation_id'] ?? null,
                            'product_name'         => $item['product_name'] ?? '',
                            'product_sku'          => $item['product_sku'] ?? null,
                            'variation_name'       => $item['variation_name'] ?? null,
                            'variation_sku'        => $item['variation_sku'] ?? null,
                            'quantity'             => $item['quantity'],
                            'unit_price'           => $item['unit_price'],
                            'line_total'           => $item['line_total'],
                        ]);
                    }

                    if ($order->user_id) {
                        UserCartItem::where('user_id', $order->user_id)->delete();
                        Log::info('[applyPaymentStatus] cart cleared for pending order', ['user_id' => $order->user_id]);
                    }
                }

                // Deduct stock_on_hand and release stock_reserved now that payment is confirmed
                $this->stock->deductForOrder($order);

                $updates['order_id'] = $order->id;
                Log::info('[applyPaymentStatus] pending order created', ['order_id' => $order->id, 'tran_id' => $tranId]);
            } elseif (!in_array($statusCode, [0, 2])) {
                // Release reserved stock on payment failure
                foreach ($paywayTxn->raw_callback['pending_order']['items'] ?? [] as $item) {
                    $stock = ProductStock::query()
                        ->where('product_id', $item['product_id'])
                        ->where(function ($q) use ($item) {
                            !empty($item['product_variation_id'])
                                ? $q->where('product_variation_id', $item['product_variation_id'])
                                : $q->whereNull('product_variation_id');
                        })
                        ->first();

                    if ($stock) {
                        $stock->stock_reserved  = max(0, (int) $stock->stock_reserved - (int) $item['quantity']);
                        $stock->stock_available = (int) $stock->stock_on_hand - (int) $stock->stock_reserved;
                        $stock->save();
                    }
                }
                Log::info('[applyPaymentStatus] pending order stock released', ['tran_id' => $tranId]);
            }

            $paywayTxn->update($updates);
            return $statusCode === 0;
        }

        Log::warning('[applyPaymentStatus] tran_id not found in donations or orders', ['tran_id' => $tranId]);
        return false;
    }

    private function parseWebhookPayload(Request $request): array
    {
        $callback = $request->all();

        if ($request->filled('data')) {
            $decoded = is_array($request->data)
                ? $request->data
                : json_decode((string) $request->data, true);

            if (is_array($decoded)) {
                $callback = array_merge($callback, $decoded);
            } else {
                Log::warning('[webhook] invalid data JSON', ['data' => $request->data]);
            }
        }

        Log::info('[webhook] normalized callback', $callback);

        $tranId = $callback['transaction_id']
            ?? $callback['tran_id']
            ?? $callback['tranId']
            ?? null;

        if (!$tranId) {
            throw new \InvalidArgumentException('PayWay callback missing tran_id.');
        }

        $statusValue = $callback['payment_status_code']
            ?? $callback['status_code']
            ?? $callback['status']
            ?? $callback['payment_status']
            ?? null;

        return [$callback, (string) $tranId, $this->normalizeStatusCode($statusValue)];
    }

    private function normalizeStatusCode(mixed $statusValue): int
    {
        if (is_numeric($statusValue)) {
            return (int) $statusValue;
        }

        return match (strtolower(trim((string) $statusValue))) {
            'success', 'successful', 'completed', 'complete', 'paid', 'approved' => 0,
            'pending' => 2,
            'declined', 'failed', 'fail', 'cancelled', 'canceled' => 3,
            'refunded' => 4,
            default => -1,
        };
    }

    private function recordPaywayCallback(string $tranId, int $statusCode, array $callback): void
    {
        $paywayTxn = PaywayTransaction::where('tran_id', $tranId)->first();

        if (!$paywayTxn) {
            return;
        }

        $rawCallback = $paywayTxn->raw_callback ?: [];
        $rawCallback['callback'] = $callback;
        $rawCallback['callback_received_at'] = now()->toDateTimeString();

        $paywayTxn->update([
            'status_code'    => (string) $statusCode,
            'payment_status' => $this->paymentStatusFromCode($statusCode),
            'raw_callback'   => $rawCallback,
        ]);
    }

    private function paymentStatusFromCode(int $statusCode): string
    {
        return match ($statusCode) {
            0       => 'paid',
            2       => 'pending',
            4       => 'refunded',
            default => 'failed',
        };
    }
}
