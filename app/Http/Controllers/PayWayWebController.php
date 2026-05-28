<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;

class PayWayWebController extends Controller
{
    /**
     * GET /payway/checkout/{tranId}
     *
     * Loads cached checkout params and auto-submits the form to ABA PayWay.
     * Mobile opens this URL in a WebView.
     */
    public function checkout(string $tranId)
    {
        $params = Cache::get('payway_checkout_' . $tranId);

        if (!$params) {
            abort(404, 'Checkout session expired or not found.');
        }

        return view('payway.checkout', compact('params'));
    }
}
