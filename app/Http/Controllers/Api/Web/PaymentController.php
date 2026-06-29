<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaywayTransaction;
use App\Models\ProductStock;
use App\Models\UserCartItem;
use App\Services\PayWayService;
use App\Services\StockService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    private PayWayService $payWay;
    private StockService  $stock;

    public function __construct(PayWayService $payWay, StockService $stock)
    {
        parent::__construct();
        $this->payWay = $payWay;
        $this->stock  = $stock;
    }

    /**
     * POST /api/web/payment/payway-checkout
     *
     * Two modes:
     *
     * Mode A — pass order_id (loads amount & customer info from order):
     *   { "order_id": 1, "payment_option": "abapay_khqr" }
     *
     * Mode B — pass raw params directly (no order required):
     *   { "amount": "12.50", "payment_option": "cards", "firstname": "John", ... }
     *
     * Supported payment_option values:
     *   - abapay_khqr           (KHQR – scan with any banking app)
     *   - cards                 (Credit / Debit card)
     *   - abapay_khqr_deeplink  (Deep-link into ABA Mobile)
     */
    public function checkout(Request $request)
    {
        $user = Auth::guard('api_web')->user();

        $validator = Validator::make($request->all(), [
            'payment_option' => 'required|in:abapay_khqr,cards,abapay_khqr_deeplink',
            // Mode A
            'order_id'       => 'nullable|integer|exists:orders,id',
            // Mode B — required only when no order_id
            'amount'         => 'nullable|numeric|min:0.01',
            'tran_id'        => 'nullable|string|max:20',
            // Customer info (optional in both modes — falls back to user profile)
            'firstname'      => 'nullable|string|max:255',
            'lastname'       => 'nullable|string|max:255',
            'email'          => 'nullable|email|max:255',
            'phone'          => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        try {
            $order = null;

            if ($request->filled('order_id')) {
                /* ── Mode A: load from order ── */
                $order = Order::where('user_id', $user->id)->findOrFail($request->order_id);

                if ($order->payment_status === 'paid') {
                    return $this->responseError('This order is already paid.');
                }

                $existingTran = PaywayTransaction::where('order_id', $order->id)
                    ->where('order_type', 'order')
                    ->value('tran_id');
                $tranId = $existingTran ?: $this->generateShortTranId('ORD');
                $amount = number_format((float) $order->grand_total, 2, '.', '');
            } else {
                /* ── Mode B: raw params ── */
                if (!$request->filled('amount')) {
                    return response()->json([
                        'message' => 'Validation failed',
                        'errors'  => ['amount' => ['The amount field is required when order_id is not provided.']],
                    ], 422);
                }

                $tranId = $request->tran_id ?? $this->generateShortTranId('TXN');
                $amount = number_format((float) $request->amount, 2, '.', '');
            }

            // Customer info: request params → user profile → empty string
            $firstName = $request->firstname ?? $user->name  ?? '';
            $lastName  = $request->lastname  ?? '';
            $email     = $request->email     ?? $user->email ?? '';
            $phone     = $request->phone     ?? $user->phone ?? '';
            $cancelUrl = $request->filled('cancel_url') && filter_var($request->cancel_url, FILTER_VALIDATE_URL)
                ? (string) $request->cancel_url
                : null;

            // Generate PayWay params — mobile posts these directly to PayWay (WebView / SDK)
            $result = $this->payWay->buildCheckoutPayload(
                $tranId,
                $amount,
                $firstName,
                $lastName,
                $email,
                $phone,
                $request->payment_option,
                $cancelUrl,
                (string) config('payway.success_url'),
            );
            $tranId = $result['tran_id'];

            // Update order payment_method if order exists
            if ($order) {
                $order->update(['payment_method' => $request->payment_option]);
            }

            PaywayTransaction::updateOrCreate(
                ['tran_id' => $tranId],
                [
                    'order_id'     => $order?->id,
                    'donation_id'  => null,
                    'tran_type'    => $request->payment_option,
                    'order_type'   => 'order',
                    'is_update'    => null,
                    'status_code'  => null,
                    'payment_status' => 'unpaid',
                ]
            );

            return $this->responseSuccess($result, 'Checkout params generated successfully.');
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }
    }

    /**
     * POST /api/web/payment/donate
     *
     * Stores pending donation payment data and returns PayWay checkout params.
     * The donation record is created only after PayWay confirms success.
     *
     * Payload:
     *   {
     *     "amount": "100.00",           // required — or pick from preset: 5, 10, 20, 50, 100, 200, 500
     *     "donation_type": "one_time",  // one_time | monthly  (default: one_time)
     *     "payment_option": "abapay_khqr",
     *     "firstname": "John",          // optional — falls back to user profile
     *     "lastname":  "Doe",
     *     "email":     "john@mail.com",
     *     "phone":     "+85512345678",
     *     "note":      "In memory of..."
     *   }
     */
    public function donate(Request $request)
    {
        $user = Auth::guard('api_web')->user();

        $validator = Validator::make($request->all(), [
            'amount'         => 'required|numeric|min:0.01',
            'donation_type'  => 'nullable|in:one_time,monthly',
            'payment_option' => 'required|in:abapay_khqr,cards,abapay_khqr_deeplink',
            'firstname'      => 'nullable|string|max:255',
            'lastname'       => 'nullable|string|max:255',
            'email'          => 'nullable|email|max:255',
            'phone'          => 'nullable|string|max:50',
            'note'           => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $firstName     = $request->firstname     ?? $user->name  ?? '';
            $lastName      = $request->lastname      ?? '';
            $email         = $request->email         ?? $user->email ?? '';
            $phone         = $request->phone         ?? $user->phone ?? '';
            $donationType  = $request->donation_type ?? 'one_time';
            $amount        = number_format((float) $request->amount, 2, '.', '');

            // Unique transaction ID prefixed with DON- so callback can distinguish from orders
            $tranId = $this->generateShortTranId('DON');

            $result = $this->payWay->buildCheckoutPayload(
                $tranId,
                $amount,
                $firstName,
                $lastName,
                $email,
                $phone,
                (string) $request->payment_option,
                $request->filled('cancel_url') && filter_var($request->cancel_url, FILTER_VALIDATE_URL)
                    ? (string) $request->cancel_url
                    : null,
                (string) config('payway.donate_success_url')
            );
            $tranId = $result['tran_id'];
            $result['payway'] = [
                'checkout_url' => $result['checkout_url'],
                'params'       => $result['params'],
            ];

            PaywayTransaction::updateOrCreate(
                ['tran_id' => $tranId],
                [
                    'order_id'     => null,
                    'donation_id'  => null,
                    'tran_type'    => $request->payment_option,
                    'order_type'   => 'donation',
                    'is_update'    => null,
                    'status_code'  => null,
                    'payment_status' => 'unpaid',
                    'raw_callback' => [
                        'pending_donation' => [
                            'user_id'        => $user?->id,
                            'donation_type'  => $donationType,
                            'amount'         => $amount,
                            'firstname'      => $firstName,
                            'lastname'       => $lastName,
                            'email'          => $email,
                            'payment_option' => $request->payment_option,
                            'note'           => $request->note,
                        ],
                    ],
                ]
            );

            DB::commit();

            return $this->responseSuccess(
                $result,
                'Donation checkout params generated successfully.'
            );
        } catch (Exception $e) {
            DB::rollBack();
            return $this->responseError($e->getMessage());
        }
    }

    /**
     * POST /api/web/payment/payway-callback
     *
     * Called by PayWay server after payment is completed.
     * No auth guard — PayWay hits this directly.
     *
     * Detects by tran_id prefix:
     *   DON-... → update donations table
     *   ORD-... → update orders table
     *
     * Expected POST params from PayWay:
     *   tran_id, apv, payment_status, hash, status_code
     */
    public function callback(Request $request)
    {
        DB::beginTransaction();
        try {
            $tranId     = $request->input('tran_id');
            $statusCode = $request->input('status_code');
            $hash       = $request->input('hash');

            // Verify hash to prevent spoofed callbacks
            if (!$this->payWay->verifyCallbackHash($tranId, $statusCode, $hash)) {
                Log::warning('PayWay callback hash mismatch', $request->all());
                return response()->json(['message' => 'Invalid hash.'], 400);
            }

            // Route to donation or order based on tran_id prefix
            if (str_starts_with($tranId, 'DON-')) {
                $this->handleDonationCallback($tranId, $statusCode);
            } else {
                $this->handleOrderCallback($tranId, $statusCode);
            }

            $paywayTxn = PaywayTransaction::where('tran_id', $tranId)->first();
            $rawCallback = [
                'callback' => $request->all(),
            ];
            if ($paywayTxn && isset($paywayTxn->raw_callback['pending_donation'])) {
                $rawCallback['pending_donation'] = $paywayTxn->raw_callback['pending_donation'];
            }

            PaywayTransaction::updateOrCreate(
                ['tran_id' => $tranId],
                [
                    'status_code'    => $statusCode,
                    'payment_status' => (string) $request->input('payment_status', ''),
                    'raw_callback'   => $rawCallback,
                ]
            );

            DB::commit();

            Log::info('PayWay callback processed', [
                'tran_id'     => $tranId,
                'status_code' => $statusCode,
            ]);

            return response()->json(['message' => 'OK']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('PayWay callback error: ' . $e->getMessage(), $request->all());
            return response()->json(['message' => 'Server error.'], 500);
        }
    }

    /* ─────────────────────────────────────────────
     | Private helpers
     ───────────────────────────────────────────── */

    private function handleOrderCallback(string $tranId, string $statusCode): void
    {
        $order = Order::where('order_no', $tranId)->first();
        if (!$order) {
            $paywayTxn = PaywayTransaction::where('tran_id', $tranId)
                ->where('order_type', 'order')
                ->first();
            if ($paywayTxn?->order_id) {
                $order = Order::find($paywayTxn->order_id);
            }
        }

        // Pending order: no order exists yet — create it on payment success
        if (!$order) {
            $paywayTxn = $paywayTxn ?? PaywayTransaction::where('tran_id', $tranId)
                ->where('order_type', 'order')
                ->whereNull('order_id')
                ->first();

            if (!$paywayTxn) {
                Log::warning('PayWay callback: order not found', ['tran_id' => $tranId]);
                return;
            }

            if ($statusCode === '0') {
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
                    }
                }

                // Deduct stock_on_hand and release stock_reserved now that payment is confirmed
                $this->stock->deductForOrder($order);

                $paywayTxn->update(['order_id' => $order->id]);
                Log::info('PayWay callback: pending order created', ['order_id' => $order->id, 'tran_id' => $tranId]);
            } elseif (!in_array($statusCode, ['0', '2'])) {
                // Release reserved stock on failure
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
                Log::info('PayWay callback: pending order stock released', ['tran_id' => $tranId]);
            }

            return;
        }

        switch ($statusCode) {
            case '0':
                $order->update(['payment_status' => 'paid', 'status' => 'confirmed']);
                break;
            case '2':
                if ($order->payment_status !== 'paid') {
                    $order->update(['payment_status' => 'pending']);
                }
                break;
            default:
                if ($order->payment_status !== 'paid') {
                    $order->update(['payment_status' => 'failed']);
                }
                break;
        }
    }

    private function handleDonationCallback(string $tranId, string $statusCode): void
    {
        $donation = Donation::where('tran_id', $tranId)->first();

        if (!$donation) {
            $paywayTxn = PaywayTransaction::where('tran_id', $tranId)
                ->where('order_type', 'donation')
                ->first();

            if ($statusCode !== '0' || !$paywayTxn) {
                Log::warning('PayWay callback: donation not ready to create', [
                    'tran_id' => $tranId,
                    'status_code' => $statusCode,
                ]);
                return;
            }

            $pending = $paywayTxn->raw_callback['pending_donation'] ?? [];
            $donation = Donation::create([
                'tran_id'        => $tranId,
                'user_id'        => $pending['user_id'] ?? null,
                'donation_type'  => $pending['donation_type'] ?? 'one_time',
                'amount'         => $pending['amount'] ?? 0,
                'firstname'      => $pending['firstname'] ?? '',
                'lastname'       => $pending['lastname'] ?? '',
                'email'          => $pending['email'] ?? '',
                'payment_option' => $pending['payment_option'] ?? $paywayTxn->tran_type,
                'payment_status' => 'paid',
                'note'           => $pending['note'] ?? null,
            ]);

            $paywayTxn->update([
                'donation_id'    => $donation->id,
                'status_code'    => $statusCode,
                'payment_status' => 'paid',
            ]);
            return;
        }

        switch ($statusCode) {
            case '0':
                $donation->update(['payment_status' => 'paid']);
                break;
            case '2':
                if ($donation->payment_status !== 'paid') {
                    $donation->update(['payment_status' => 'pending']);
                }
                break;
            default:
                if ($donation->payment_status !== 'paid') {
                    $donation->update(['payment_status' => 'failed']);
                }
                break;
        }
    }

    private function generateShortTranId(string $prefix): string
    {
        // PayWay requires tran_id <= 20 chars.
        // Format: PREFIX(3) + ymdHis(12) + rand(5) = 20 chars.
        $p = strtoupper(substr($prefix, 0, 3));
        return $p . now()->format('ymdHis') . strtoupper(substr(md5(uniqid('', true)), 0, 5));
    }
}
