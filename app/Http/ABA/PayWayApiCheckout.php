<?php

namespace App\Http\ABA;

/*
|--------------------------------------------------------------------------
| ABA PayWay API URL
|--------------------------------------------------------------------------
| API URL that is provided by PayWay must be required in your post form
|
*/
define('ABA_PAYWAY_API_URL', env('ABA_PAYWAY_API_URL'));

/*
|--------------------------------------------------------------------------
| ABA PayWay API KEY
|--------------------------------------------------------------------------
| API KEY that is generated and provided by PayWay must be required in your post form
|
*/
define('ABA_PAYWAY_API_KEY', env('ABA_PAYWAY_API_KEY'));

/*
|--------------------------------------------------------------------------
| ABA PayWay Merchant ID
|--------------------------------------------------------------------------
| Merchant ID that is generated and provided by PayWay must be required in your post form
|
*/
define('ABA_PAYWAY_MERCHANT_ID', env('ABA_PAYWAY_MERCHANT_ID'));

/*
|--------------------------------------------------------------------------
| ABA PayWay Check Transaction status
|--------------------------------------------------------------------------
| For make sure the transaction of customer is success or not
|
*/
define('ABA_PAYWAY_API_CHECK_TRANSACTION', env('ABA_PAYWAY_API_CHECK_TRANSACTION'));


class PayWayApiCheckout {

    /**
     * Returns the getHash
     * For PayWay security, you must follow the way of encryption for hash.
     *
     * @param string $transactionId
     * @param string $amount
     *
     * @return string getHash
     */
    public static function getHash($hash_str) {
//      echo $hash_str; die;
        $hash = base64_encode(hash_hmac('sha512', $hash_str, ABA_PAYWAY_API_KEY, true));
        return $hash;
    }

    /**
     * Returns the getApiUrl
     *
     * @return string getApiUrl
     */
    public static function getApiUrl() {
        return ABA_PAYWAY_API_URL;
    }

    /**
     * Returns the getApiKey
     *
     * @return string getApiKey
     */
    public static function getApiKey() {
        return ABA_PAYWAY_API_KEY;
    }

    /**
     * Returns the getMerchantId
     *
     * @return string getMerchantId
     */
    public static function getMerchantId() {
        return ABA_PAYWAY_MERCHANT_ID;
    }

    /**
     * Returns the getApiCheckTransactionUrl
     *
     * @return string getApiCheckTransactionUrl
     */
    public static function getApiCheckTransactionUrl() {
        return ABA_PAYWAY_API_CHECK_TRANSACTION;
    }
}
