<?php

namespace App\Http\Controllers;

use App\Http\ABA\PayWayApiCheckout;
use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\Order;
use App\Models\UserCartItem;
use App\Services\PayWayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaywayController extends Controller
{
    private PayWayService $payWay;

    protected $arrColumnNames = ['amount', 'name', 'email', 'phone', 'payment_option'];

    public function __construct(PayWayService $payWay)
    {
        $this->payWay = $payWay;
    }

    public function index()
    {
        return view('pages.payway.viewData');
    }

    public function length_charecter_60($string, $addon)
    {
        $string = substr($string, 0, 60);
        if (strlen($string) < 10) {
            $string = $string . ' ' . $addon;
        }
        if (strlen($string) > 60) {
            $string = substr($string, 0, 60);
        }
        return $string;
    }

    public function remove_special_sign($string)
    {
        $pattern = '/[^a-z A-Z0-9\-]/i';
        $replacement = '';
        return preg_replace($pattern, $replacement, $string);
    }

    public function getHash($concat_params, $ABA_PAYWAY_API_KEY)
    {
        return base64_encode(hash_hmac('sha512', $concat_params, $ABA_PAYWAY_API_KEY, true));
    }

    /**
     * POST /api/web/create-payment
     *
     * For donation (no order_id):
     *   { "amount", "name", "lastname", "phone", "email", "payment_option", "donation_type", "note" }
     *
     * For order (with order_id):
     *   { "order_id", "name", "lastname", "phone", "email", "payment_option" }
     *   amount is read from orders.grand_total — not accepted from input.
     */
    public function payway_form(Request $request)
    {
        try {
            Log::info('PaywayController => payway_form > start : ' . json_encode($request->all()));

            $ABA_PAYWAY_API_URL     = PayWayApiCheckout::getApiUrl();
            $ABA_PAYWAY_API_KEY     = PayWayApiCheckout::getApiKey();
            $ABA_PAYWAY_MERCHANT_ID = PayWayApiCheckout::getMerchantId();
            $input                  = $request->all();
            $orderId                = $input['order_id'] ?? null;

            // For order flow, amount comes from the order — skip 'amount' check
            $requiredFields = $orderId
                ? ['name', 'email', 'phone', 'payment_option']
                : $this->arrColumnNames;

            foreach ($requiredFields as $field) {
                if (!array_key_exists($field, $input)) {
                    Log::debug('PaywayController => payway_form > missing field: ' . $field);
                    return response()->json(['message' => "Missing required field: $field"], 422);
                }
            }

            $allowedOptions = ['cards', 'abapay_khqr', 'abapay_khqr_deeplink'];
            if (!in_array($input['payment_option'], $allowedOptions)) {
                return response()->json(['message' => 'payment_option must be one of: ' . implode(', ', $allowedOptions)], 422);
            }

            DB::beginTransaction();

            $req_time             = time();
            $tran_id              = $this->payWay->generateTranId();
            $firstname            = $input['name'] ?? auth()->user()->name ?? '';
            $lastname             = $input['lastname'] ?? '';
            $phone                = $input['phone'] ?? auth()->user()->phone ?? '';
            $email                = $input['email'] ?? auth()->user()->email ?? '';
            $payment_option       = $input['payment_option'];
            $shipping             = $input['shipping'] ?? '';
            $view_type            = "hosted_view";
            $return_url           = base64_encode(config('app.url') . '/payway-submit');
            $continue_success_url = config('app.url') . '/status=success';
            $cancel_url           = config('app.url') . '/status=cancel';
            $return_params        = "json";

            if ($orderId) {
                $order = Order::findOrFail($orderId);

                if ($order->payment_status === 'paid') {
                    DB::rollBack();
                    return response()->json(['message' => 'Order is already paid.'], 422);
                }

                $amount = number_format((float) $order->grand_total, 2, '.', '');

                $order->update([
                    'order_no'       => $tran_id,
                    'payment_method' => $payment_option,
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
                    'payment_option' => $payment_option,
                    'payment_status' => 'pending',
                    'note'           => $input['note'] ?? null,
                ]);
            }

            $concat_params = $req_time .
                $ABA_PAYWAY_MERCHANT_ID .
                $tran_id .
                $amount .
                $shipping .
                $firstname .
                $lastname .
                $email .
                $phone .
                $payment_option .
                $return_url .
                $cancel_url .
                $continue_success_url .
                $return_params;

            $hash = $this->getHash($concat_params, $ABA_PAYWAY_API_KEY);

            $params = [
                "hash"                 => $hash,
                "req_time"             => $req_time,
                "merchant_id"          => $ABA_PAYWAY_MERCHANT_ID,
                "tran_id"              => $tran_id,
                "amount"               => $amount,
                "firstname"            => $firstname,
                "lastname"             => $lastname,
                "phone"                => $phone,
                "payment_option"       => $payment_option,
                "view_type"            => $view_type,
                "return_url"           => $return_url,
                "continue_success_url" => $continue_success_url,
                "cancel_url"           => $cancel_url,
                "return_params"        => $return_params,
            ];

            $PAYMENT_PURCHASE = $this->ABA_PAYWAY_PURCHASE_PAYMENT($ABA_PAYWAY_API_URL, $params);

            DB::commit();

            Log::debug('PaywayController => payway_form > end');

            return $PAYMENT_PURCHASE;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('PaywayController => payway_form > error : ' . $e);
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function ABA_PAYWAY_PURCHASE_PAYMENT($ABA_PAYWAY_API_URL, $params)
    {
        return Http::post($ABA_PAYWAY_API_URL, $params);
    }

    public function generateTranId()
    {
        return response()->json(['tran_id' => $this->payWay->generateTranId()]);
    }

    public function paymentSubmit(Request $req)
    {
        Log::info('[paymentSubmit] raw payload', $req->all());

        DB::beginTransaction();
        try {
            $dataCallback = $req->data ? (object) json_decode($req->data) : null;

            if (!$dataCallback) {
                return 'payment_fail';
            }

            Log::info('[paymentSubmit] decoded callback', (array) $dataCallback);

            $tranId     = $dataCallback->transaction_id ?? $dataCallback->tran_id ?? null;
            $statusCode = (int) ($dataCallback->payment_status_code ?? $dataCallback->status ?? -1);

            $result = $this->applyPaymentStatus($tranId, $statusCode);

            DB::commit();
            return $result ? 'payment_success' : 'payment_fail';
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('[paymentSubmit] exception: ' . $e->getMessage());
            return 'payment_fail';
        }
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
                default => $order->status,
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
