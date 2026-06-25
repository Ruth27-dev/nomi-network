<?php

return [

    /*
    |--------------------------------------------------------------------------
    | ABA PayWay Merchant Configuration
    |--------------------------------------------------------------------------
    | Set these values in your .env file.
    | Switch PAYWAY_API_URL to the live URL when going to production:
    |   https://checkout.payway.com.kh/api/payment-gateway/v1/payments/purchase
    */

    'merchant_id'   => env('PAYWAY_MERCHANT_ID', env('ABA_PAYWAY_MERCHANT_ID', '')),
    'api_key'       => env('PAYWAY_API_KEY', env('ABA_PAYWAY_API_KEY', '')),
    'public_key'    => env('PAYWAY_PUBLIC_KEY', env('ABA_PAYWAY_PUBLIC_KEY', env('PAYWAY_API_KEY', env('ABA_PAYWAY_API_KEY', '')))),
    'api_url'       => env('PAYWAY_API_URL', env('ABA_PAYWAY_API_URL', 'https://checkout-sandbox.payway.com.kh/api/payment-gateway/v1/payments/purchase')),
    'checkout_url'  => env('PAYWAY_CHECKOUT_URL', 'https://checkout-sandbox.payway.com.kh'),
    'return_url'    => 'https://admin.nomihandicraftandservice.org/api/web/payway-webhook',
    'cancel_url'    => env('PAYWAY_CANCEL_URL', env('APP_URL', 'http://localhost')),
    'success_url'   => env('PAYWAY_SUCCESS_URL', env('APP_URL', 'http://localhost')),

];
