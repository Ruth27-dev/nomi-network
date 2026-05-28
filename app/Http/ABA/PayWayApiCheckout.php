<?php

namespace App\Http\ABA;
/*
|--------------------------------------------------------------------------
| ABA PayWay API URL
|--------------------------------------------------------------------------
| API URL that is provided by PayWay must be required in your post form
|
*/


// define('ABA_PAYWAY_API_URL', 'https://payway.ababank.com/api/pwsalesonlineotrainingt/');

/*
|--------------------------------------------------------------------------
| ABA PayWay API KEY
|--------------------------------------------------------------------------
| API KEY that is generated and provided by PayWay must be required in your post form
|
*/
// define('ABA_PAYWAY_API_KEY', 'bd126ddebd04a5e437796e31872f8f77');

/*
|--------------------------------------------------------------------------
| ABA PayWay Merchant ID
|--------------------------------------------------------------------------
| Merchant ID that is generated and provided by PayWay must be required in your post form
|
*/
// define('ABA_PAYWAY_MERCHANT_ID', 'saleonlinetraining');

// testing
define('ABA_PAYWAY_API_KEY', 'bc28db5f-5155-4263-8446-a812d9907c61');
define('ABA_PAYWAY_MERCHANT_ID', 'nomihandicraftnservice');
define('ABA_PAYWAY_API_URL', 'https://checkout-sandbox.payway.com.kh/api/payment-gateway/v1/payments/purchase');

class PayWayApiCheckout
{

    /**
     * Returns the getHash
     * For PayWay security, you must follow the way of encryption for hash.
     *
     * @param string $string
     *
     * @return string getHash
     */
    public static function getHash($string = null)
    {
        $hash = base64_encode(hash_hmac('sha512', $string, ABA_PAYWAY_API_KEY, true));
        return $hash;
    }

    /**
     * Returns the getApiUrl
     *
     * @return string getApiUrl
     */

    public static function chechTransactionId($transactionId)
    {

        $chechTran = base64_encode(hash_hmac('sha512', ABA_PAYWAY_MERCHANT_ID . $transactionId, ABA_PAYWAY_API_KEY, true));
        return $chechTran;
    }

    public static function getApiUrl()
    {
        return ABA_PAYWAY_API_URL;
    }

    public static function getMerchant()
    {
        return ABA_PAYWAY_MERCHANT_ID;
    }
    public static function getUrl()
    {

        $urlArray = explode('/api', ABA_PAYWAY_API_URL);
        return $urlArray[0];
    }

    public static function getUniqueTranId()
    {

        $unique_id = 'STUDENT-' . mt_rand(100000, 999999);;
        return $unique_id;
    }
    /**
     *
     * return get Request Time
     */
    public static function getReqTime()
    {
        return date('YmdHis');
    }
    public static function getReturnUrl()
    {
        $url = route('paymentAbaPushBack');
        return base64_encode($url);
    }
    public static function getReturnUrlCourse()
    {
        $url = route('paymentAbaPushBackCourse');
        return base64_encode($url);
    }
}
