<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;

class PayWayWebController extends Controller
{
    /**
     * GET /payway/checkout/{tranId}
     *
     * Opens the ABA PayWay popup checkout with the cached purchase params.
     */
    public function checkout(string $tranId)
    {
        $params = Cache::get('payway_checkout_' . $tranId);

        if (!$params) {
            abort(404, 'Checkout session expired or not found.');
        }

        return view('payway.checkout', [
            'params' => $params,
        ]);
    }
}
