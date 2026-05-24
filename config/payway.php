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

    'merchant_id'   => env('PAYWAY_MERCHANT_ID', ''),
    'api_key'       => env('PAYWAY_API_KEY', ''),
    'api_url'       => env('PAYWAY_API_URL', 'https://checkout-sandbox.payway.com.kh/api/payment-gateway/v1/payments/purchase'),
    'checkout_url'  => env('PAYWAY_CHECKOUT_URL', 'https://checkout-sandbox.payway.com.kh'),

];
