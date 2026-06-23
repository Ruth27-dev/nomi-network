<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class PayWayService
{
    private string $merchantId;
    private string $apiKey;
    private string $publicKey;
    private string $apiUrl;
    private string $checkoutBaseUrl;

    public function __construct()
    {
        $this->merchantId = (string) config('payway.merchant_id', '');
        $this->apiKey = (string) config('payway.api_key', '');
        $this->publicKey = (string) config('payway.public_key', $this->apiKey);
        if ($this->publicKey === '') {
            $this->publicKey = $this->apiKey;
        }
        $this->apiUrl = (string) config('payway.api_url', '');
        $this->checkoutBaseUrl = (string) config('payway.checkout_url', '');
    }

    public function getMerchantId(): string
    {
        return $this->merchantId;
    }

    public function generateTranId(): string
    {
        return now()->format('ymdHis').strtoupper(substr(md5(uniqid('', true)), 0, 8));
    }

    public function getApiUrl(): string
    {
        return $this->apiUrl;
    }

    public function getReqTime(): string
    {
        return now()->format('YmdHis');
    }

    public function generateHash(
        string $reqTime,
        string $tranId,
        string $amount,
        string $firstName,
        string $lastName,
        string $email,
        string $phone,
        string $paymentOption,
        string $returnUrl = '',
        string $cancelUrl = '',
        string $continueSuccessUrl = '',
        string $currency = 'USD',
        string $lifetime = '',
        string $type = 'purchase'
    ): string {
        $hashStr = $reqTime
            .$this->merchantId
            .$tranId
            .$amount
            .$firstName
            .$lastName
            .$email
            .$phone
            .$type
            .$paymentOption
            .$returnUrl
            .$cancelUrl
            .$continueSuccessUrl
            .$currency
            .$lifetime;

        return base64_encode(hash_hmac('sha512', $hashStr, $this->apiKey, true));
    }

    public function verifyCallbackHash(string $tranId, string $statusCode, string $receivedHash): bool
    {
        $expected = base64_encode(hash_hmac('sha512', $tranId.$statusCode, $this->apiKey, true));

        return hash_equals($expected, $receivedHash);
    }

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
            $concatParams = $reqTime
                .$this->merchantId
                .$tranId
                .$amount
                .$firstName
                .$lastName
                .$email
                .$phone
                .'purchase'
                .$paymentOption
                .$encodedReturnUrl
                .$continueSuccessUrl;
        } else {
            $concatParams = $reqTime
                .$this->merchantId
                .$tranId
                .$amount
                .$firstName
                .$lastName
                .$email
                .$phone
                .$paymentOption
                .$encodedReturnUrl
                .$cancelUrl
                .$continueSuccessUrl
                .$returnParams;
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
            'view_type' => 'popup',
            'return_url' => $encodedReturnUrl,
            'continue_success_url' => $continueSuccessUrl,
            'cancel_url' => $cancelUrl,
            'return_params' => $returnParams,
        ];
    }

    public function getTransactionDetail(string $tranId): array
    {
        $url = rtrim($this->checkoutBaseUrl, '/').'/api/payment-gateway/v1/payments/transaction-detail';

        return $this->fetchTransaction($tranId, $url);
    }

    public function checkTransaction(string $tranId): array
    {
        $url = rtrim($this->checkoutBaseUrl, '/').'/api/payment-gateway/v1/payments/check-transaction-2';

        return $this->fetchTransaction($tranId, $url);
    }

    private function fetchTransaction(string $tranId, string $url): array
    {
        $reqTime = now()->format('YmdHis');
        $hash = base64_encode(hash_hmac('sha512', $reqTime.$this->merchantId.$tranId, $this->apiKey, true));

        try {
            $response = Http::timeout(30)->post($url, [
                'req_time' => $reqTime,
                'merchant_id' => $this->merchantId,
                'tran_id' => $tranId,
                'hash' => $hash,
            ]);

            return [
                'ok' => $response->successful(),
                'data' => $response->json(),
            ];
        } catch (\Throwable $e) {
            return [
                'ok' => false,
                'data' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function purchase(array $params): array
    {
        try {
            $response = Http::asForm()->timeout(30)->post($this->apiUrl, $params);
            $json = $response->json();

            return [
                'status' => $json['status'] ?? null,
                'qr_string' => $json['qr_string'] ?? null,
                'abapay_deeplink' => $json['abapay_deeplink'] ?? null,
                'checkout_qr_url' => $json['checkout_qr_url'] ?? null,
            ];
        } catch (\Throwable $e) {
            return [
                'status' => ['code' => '99', 'message' => $e->getMessage()],
                'qr_string' => null,
                'abapay_deeplink' => null,
                'checkout_qr_url' => null,
            ];
        }
    }

    public function buildCheckoutPayload(
        string $tranId,
        string $amount,
        string $firstName,
        string $lastName,
        string $email,
        string $phone,
        string $paymentOption,
        ?string $cancelUrl = null,
        ?string $successUrl = null,
        string $currency = 'USD',
        ?int $lifetime = 5
    ): array {
        $tranId = trim($tranId, "/ \t\n\r\0\x0B");
        $amount = number_format((float) $amount, 2, '.', '');
        $currency = strtoupper($currency ?: 'USD');
        $reqTime = $this->getReqTime();
        $returnUrl = base64_encode((string) config('payway.return_url'));
        $cancelUrl = $cancelUrl ?? (string) config('payway.cancel_url');
        $continueSuccessUrl = $successUrl ?? (string) config('payway.success_url');
        $type = 'purchase';
        $lifetime = 5;

        $hash = $this->generateHash(
            $reqTime,
            $tranId,
            $amount,
            $firstName,
            $lastName,
            $email,
            $phone,
            $paymentOption,
            returnUrl: $returnUrl,
            cancelUrl: $cancelUrl,
            continueSuccessUrl: $continueSuccessUrl,
            currency: $currency,
            lifetime: (string) $lifetime,
            type: $type
        );

        $params = [
            'api_url' => $this->apiUrl,
            'merchant_id' => $this->merchantId,
            'tran_id' => $tranId,
            'req_time' => $reqTime,
            'hash' => $hash,
            'amount' => $amount,
            'firstname' => $firstName,
            'lastname' => $lastName,
            'email' => $email,
            'phone' => $phone,
            'type' => $type,
            'payment_option' => $paymentOption,
            'view_type' => 'popup',
            'return_url' => $returnUrl,
            'cancel_url' => $cancelUrl,
            'continue_success_url' => $continueSuccessUrl,
            'currency' => $currency,
            'lifetime' => $lifetime,
        ];

        Cache::put('payway_checkout_'.$tranId, $params, now()->addMinutes(10));

        return [
            'tran_id' => $tranId,
            'checkout_url' => url('payway/checkout/'.rawurlencode($tranId)),
            'params' => $params,
        ];
    }
}
