<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class PayWayService
{
    private string $merchantId;
    private string $apiKey;
    private string $apiUrl;
    private string $checkoutBaseUrl;

    public function __construct()
    {
        $this->merchantId      = (string) config('payway.merchant_id', '');
        $this->apiKey          = (string) config('payway.api_key', '');
        $this->apiUrl          = (string) config('payway.api_url', '');
        $this->checkoutBaseUrl = (string) config('payway.checkout_url', '');
    }

    public function getMerchantId(): string
    {
        return $this->merchantId;
    }

    public function generateTranId(): string
    {
        return now()->format('ymdHis') . strtoupper(substr(md5(uniqid('', true)), 0, 8));
    }

    public function getApiUrl(): string
    {
        return $this->apiUrl;
    }

    public function getReqTime(): string
    {
        return (string) time();
    }

    /**
     * Generate HMAC-SHA512 hash for PayWay checkout.
     * Hash string: req_time + merchant_id + tran_id + amount + firstname + lastname + email + phone + payment_option
     */
    public function generateHash(
        string $reqTime,
        string $tranId,
        string $amount,
        string $firstName,
        string $lastName,
        string $email,
        string $phone,
        string $paymentOption
    ): string {
        $hashStr = $reqTime
            . $this->merchantId
            . $tranId
            . $amount
            . $firstName
            . $lastName
            . $email
            . $phone
            . $paymentOption;

        return base64_encode(hash_hmac('sha512', $hashStr, $this->apiKey, true));
    }

    /**
     * Verify the callback hash sent by PayWay.
     * PayWay signs callbacks with: tran_id + status_code
     */
    public function verifyCallbackHash(string $tranId, string $statusCode, string $receivedHash): bool
    {
        $expected = base64_encode(hash_hmac('sha512', $tranId . $statusCode, $this->apiKey, true));
        return hash_equals($expected, $receivedHash);
    }

    /**
     * Build PayWay hosted-view params compatible with legacy and current flows.
     */
    public function buildHostedPurchaseParams(
        string $tranId,
        string $amount,
        string $firstName,
        string $lastName,
        string $email,
        string $phone,
        string $paymentOption,
        string $returnUrl,
        string $cancelUrl,
        string $continueSuccessUrl,
        string $returnParams = 'json',
        string $hashMode = 'default'
    ): array {
        $reqTime = $this->getReqTime();
        $encodedReturnUrl = base64_encode($returnUrl);
        $amount = number_format((float) $amount, 2, '.', '');

        if ($hashMode === 'legacy_purchase') {
            // DreamZone-compatible format:
            // req_time + merchant_id + tran_id + amount + firstname + lastname + email + phone + 'purchase' + payment_option + return_url + continue_success_url
            $concatParams = $reqTime
                . $this->merchantId
                . $tranId
                . $amount
                . $firstName
                . $lastName
                . $email
                . $phone
                . 'purchase'
                . $paymentOption
                . $encodedReturnUrl
                . $continueSuccessUrl;
        } else {
            $concatParams = $reqTime
                . $this->merchantId
                . $tranId
                . $amount
                . $firstName
                . $lastName
                . $email
                . $phone
                . $paymentOption
                . $encodedReturnUrl
                . $cancelUrl
                . $continueSuccessUrl
                . $returnParams;
        }

        $hash = base64_encode(hash_hmac('sha512', $concatParams, $this->apiKey, true));

        return [
            'hash' => $hash,
            'req_time' => $reqTime,
            'merchant_id' => $this->merchantId,
            'tran_id' => $tranId,
            'amount' => $amount,
            'firstname' => $firstName,
            'lastname' => $lastName,
            'email' => $email,
            'phone' => $phone,
            'payment_option' => $paymentOption,
            'view_type' => 'hosted_view', //hosted_view
            'return_url' => $encodedReturnUrl,
            'continue_success_url' => $continueSuccessUrl,
            'cancel_url' => $cancelUrl,
            'return_params' => $returnParams,
        ];
    }

    public function getTransactionDetail(string $tranId): array
    {
        $reqTime = now()->format('YmdHis');
        $hash    = base64_encode(hash_hmac('sha512', $reqTime . $this->merchantId . $tranId, $this->apiKey, true));

        $url = rtrim($this->checkoutBaseUrl, '/') . '/api/payment-gateway/v1/payments/transaction-detail';

        try {
            $response = Http::timeout(30)->post($url, [
                'req_time'    => $reqTime,
                'merchant_id' => $this->merchantId,
                'tran_id'     => $tranId,
                'hash'        => $hash,
            ]);

            return [
                'ok'   => $response->successful(),
                'data' => $response->json(),
            ];
        } catch (\Throwable $e) {
            return [
                'ok'    => false,
                'data'  => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function purchase(array $params): array
    {
        try {
            $response = Http::timeout(30)->post($this->apiUrl, $params);

            $json = $response->json();

            return [
                'status'          => $json['status']          ?? null,
                'qr_string'       => $json['qr_string']       ?? null,
                'abapay_deeplink' => $json['abapay_deeplink'] ?? null,
                'checkout_qr_url' => $json['checkout_qr_url'] ?? null,
            ];
        } catch (\Throwable $e) {
            return [
                'status'          => ['code' => '99', 'message' => $e->getMessage()],
                'qr_string'       => null,
                'abapay_deeplink' => null,
                'checkout_qr_url' => null,
            ];
        }
    }

    /**
     * Build checkout params, cache them, and return a checkout_url for mobile to open in WebView.
     * Cache TTL: 10 minutes.
     */
    public function buildCheckoutPayload(
        string $tranId,
        string $amount,
        string $firstName,
        string $lastName,
        string $email,
        string $phone,
        string $paymentOption
    ): array {
        $reqTime = $this->getReqTime();

        $hash = $this->generateHash(
            $reqTime,
            $tranId,
            $amount,
            $firstName,
            $lastName,
            $email,
            $phone,
            $paymentOption
        );

        $params = [
            'api_url'        => $this->apiUrl,
            'merchant_id'    => $this->merchantId,
            'tran_id'        => $tranId,
            'req_time'       => $reqTime,
            'hash'           => $hash,
            'amount'         => $amount,
            'firstname'      => $firstName,
            'lastname'       => $lastName,
            'email'          => $email,
            'phone'          => $phone,
            'payment_option' => $paymentOption,
        ];

        // Cache params for 10 minutes — the web checkout page reads from here
        Cache::put('payway_checkout_' . $tranId, $params, now()->addMinutes(10));

        return [
            'tran_id'      => $tranId,
            'checkout_url' => route('payway.checkout', ['tranId' => $tranId]),
        ];
    }
}
