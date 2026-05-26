<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class PayWayService
{
    private string $merchantId;
    private string $apiKey;
    private string $apiUrl;

    public function __construct()
    {
        $this->merchantId = env('PAYWAY_MERCHANT_ID', '');
        $this->apiKey     = env('PAYWAY_API_KEY', '');
        $this->apiUrl     = env('PAYWAY_API_URL', '');
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
     * Call ABA PayWay API directly from the server and return the response.
     * Mobile receives the PayWay response (QR, deep-link, etc.) directly.
     *
     * @throws Exception
     */
    public function purchase(
        string $tranId,
        string $amount,
        string $firstName,
        string $lastName,
        string $email,
        string $phone,
        string $paymentOption
    ): array {
        $reqTime = (string) time();

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
            'hash'           => $hash,
            'tran_id'        => $tranId,
            'amount'         => $amount,
            'firstname'      => $firstName,
            'lastname'       => $lastName,
            'email'          => $email,
            'phone'          => $phone,
            'req_time'       => $reqTime,
            'merchant_id'    => $this->merchantId,
            'payment_option' => $paymentOption,
        ];

        $response = Http::timeout(30)
            ->asForm()
            ->post($this->apiUrl, $params);

        if ($response->failed()) {
            throw new Exception('PayWay API error: ' . $response->status() . ' — ' . $response->body());
        }

        return [
            'status'   => $response->status(),
            'response' => $response->json() ?? $response->body(),
            'params'   => $params, // also return the params sent (useful for debugging)
        ];
    }
}
