<?php

namespace App\Http\Controllers;

use App\Services\PayWayService;
use Illuminate\Support\Facades\Cache;

class PayWayWebController extends Controller
{
    public function __construct(private PayWayService $payWay) {}

    /**
     * GET /payway/checkout/{tranId}
     *
     * - cards: auto-submits form to ABA hosted payment page
     * - abapay_khqr / abapay_khqr_deeplink: calls ABA API server-side, shows QR code
     */
    public function checkout(string $tranId)
    {
        $params = Cache::get('payway_checkout_' . $tranId);

        if (!$params) {
            abort(404, 'Checkout session expired or not found.');
        }

        $paymentOption = $params['payment_option'] ?? '';

        if (in_array($paymentOption, ['abapay_khqr', 'abapay_khqr_deeplink'])) {
            $purchaseParams = array_diff_key($params, ['api_url' => '']);
            $result = $this->payWay->purchase($purchaseParams);

            return view('payway.checkout', [
                'mode'   => 'qr',
                'result' => $result,
                'params' => $params,
            ]);
        }

        return view('payway.checkout', [
            'mode'   => 'redirect',
            'params' => $params,
        ]);
    }
}
