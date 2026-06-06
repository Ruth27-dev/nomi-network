<!DOCTYPE html>
<html lang="en">
<head>
    <title>PayWay Checkout</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="author" content="PayWay">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
</head>
<body>
    <div id="aba_main_modal" class="aba-modal">
        <div class="aba-modal-content">
            <form id="aba_merchant_request" target="aba_webservice" action="{{ $params['api_url'] }}" method="POST">
                <input type="hidden" name="hash" value="{{ $params['hash'] }}" id="hash">
                <input type="hidden" name="tran_id" value="{{ $params['tran_id'] }}" id="tran_id">
                <input type="hidden" name="amount" value="{{ $params['amount'] }}" id="amount">
                <input type="hidden" name="firstname" value="{{ $params['firstname'] }}">
                <input type="hidden" name="lastname" value="{{ $params['lastname'] }}">
                <input type="hidden" name="email" value="{{ $params['email'] }}">
                <input type="hidden" name="phone" value="{{ $params['phone'] }}">
                <input type="hidden" name="req_time" value="{{ $params['req_time'] }}">
                <input type="hidden" name="merchant_id" value="{{ $params['merchant_id'] }}">
                <input type="hidden" name="payment_option" value="{{ $params['payment_option'] ?? 'abapay_khqr' }}">
                <input type="hidden" name="view_type" value="{{ $params['view_type'] ?? 'popup' }}">
            </form>
        </div>
    </div>

    <div class="container" style="height: 80vh;"></div>

    <script src="https://checkout.payway.com.kh/plugins/checkout2-0.js"></script>
    <script>
        $(document).ready(function(){
            AbaPayway.checkout();
        });
    </script>
</body>
</html>
