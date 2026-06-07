<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\Order;
use App\Models\UserCartItem;
use App\Services\PayWayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaywayController extends Controller
{
    private PayWayService $payWay;

    public function __construct(PayWayService $payWay)
    {
        $this->payWay = $payWay;
    }

    public function index()
    {
        return view('pages.payway.viewData');
    }

    /**
     * POST /api/web/create-payment
     *
     * For donation (no order_id):
     *   { "amount", "firstname", "lastname", "phone", "donation_type", "note" }
     *
     * For order (with order_id):
     *   { "order_id", "firstname", "lastname", "phone" }
     *   amount is read from orders.grand_total — not accepted from input.
     */
    public function payway_form(Request $request)
    {
        $input     = $request->all();
        $orderId   = $input['order_id'] ?? null;
        $required  = $orderId
            ? ['order_id', 'firstname', 'lastname', 'phone', 'payment_option']
            : ['amount', 'firstname', 'lastname', 'phone', 'payment_option'];

        $allowedOptions = ['cards', 'abapay_khqr', 'abapay_khqr_deeplink'];
        if (isset($input['payment_option']) && !in_array($input['payment_option'], $allowedOptions)) {
            return response()->json(['message' => 'payment_option must be one of: ' . implode(', ', $allowedOptions)], 422);
        }

        foreach ($required as $field) {
            if (!array_key_exists($field, $input)) {
                return response()->json(['message' => "Missing required field: $field"], 422);
            }
        }

        DB::beginTransaction();
        try {
            $tran_id       = $this->payWay->generateTranId();
            $firstname     = $input['firstname'];
            $lastname      = $input['lastname'];
            $phone         = $input['phone'];
            $email         = $input['email'] ?? '';
            $paymentOption = $input['payment_option'];
            $cancelUrl     = !empty($input['cancel_url']) && filter_var($input['cancel_url'], FILTER_VALIDATE_URL)
                ? (string) $input['cancel_url']
                : null;

            if ($orderId) {
                $order = Order::findOrFail($orderId);

                if ($order->payment_status === 'paid') {
                    return response()->json(['message' => 'Order is already paid.'], 422);
                }

                // Use actual grand_total — never trust client-sent amount for orders
                $amount = number_format((float) $order->grand_total, 2, '.', '');

                // Store tran_id on order so webhook can look it up
                $order->update([
                    'order_no'       => $tran_id,
                    'payment_method' => $paymentOption,
                    'payment_status' => 'pending',
                ]);
            } else {
                $amount = number_format((float) $input['amount'], 2, '.', '');

                Donation::create([
                    'tran_id'        => $tran_id,
                    'user_id'        => null,
                    'donation_type'  => $input['donation_type'] ?? 'one_time',
                    'amount'         => $amount,
                    'firstname'      => $firstname,
                    'lastname'       => $lastname,
                    'payment_option' => $paymentOption,
                    'payment_status' => 'pending',
                    'note'           => $input['note'] ?? null,
                ]);
            }

            $checkoutPayload = $this->payWay->buildCheckoutPayload(
                $tran_id,
                $amount,
                $firstname,
                $lastname,
                $email,
                $phone,
                $paymentOption,
                $cancelUrl
            );

            DB::commit();

            return response()->json([
                'tran_id' => $tran_id,
                'data'    => $checkoutPayload,
            ]);
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
            $dataCallback = $req->data ? (object) json_decode($req->data) : null;

            Log::info('[webhook] decoded callback', (array) $dataCallback);

            $tranId     = $dataCallback->transaction_id ?? $dataCallback->tran_id ?? null;
            $statusCode = (int) ($dataCallback->payment_status_code ?? -1);

            $result = $this->applyPaymentStatus($tranId, $statusCode);

            DB::commit();
            Log::info('[webhook] done', ['tran_id' => $tranId, 'status_code' => $statusCode]);
            return $result ? 'payment_success' : 'payment_fail';
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('[webhook] exception: ' . $e->getMessage());
            return 'payment_fail';
        }
    }

    public function checkTransaction(Request $request)
    {
        $tranId = $request->input('tran_id');

        if (!$tranId) {
            return response()->json(['message' => 'tran_id is required'], 422);
        }

        $detail = $this->payWay->getTransactionDetail($tranId);

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

        Log::warning('[applyPaymentStatus] tran_id not found in donations or orders', ['tran_id' => $tranId]);
        return false;
    }
}
