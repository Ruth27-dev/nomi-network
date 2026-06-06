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
    <div class="container" style="height: 80vh;">
        <form id="aba_merchant_request" target="aba_webservice" action="{{ $params['api_url'] }}" method="POST" class="right" style="flex-direction: column; align-items: flex-start;">
            <input type="hidden" name="req_time" value="{{ $params['req_time'] }}">
            <input type="hidden" name="merchant_id" value="{{ $params['merchant_id'] }}">
            <input type="hidden" name="tran_id" value="{{ $params['tran_id'] }}" id="tran_id">
            <input type="hidden" name="amount" value="{{ $params['amount'] }}" id="amount">

            @if (!empty($params['items']))
                <input type="hidden" name="items" value="{{ $params['items'] }}">
            @endif

            <input type="hidden" name="firstname" value="{{ $params['firstname'] }}">
            <input type="hidden" name="lastname" value="{{ $params['lastname'] }}">
            <input type="hidden" name="email" value="{{ $params['email'] }}">
            <input type="hidden" name="phone" value="{{ $params['phone'] }}">
            <input type="hidden" name="payment_option" value="{{ $params['payment_option'] ?? 'abapay_khqr' }}">
            <input type="hidden" name="view_type" value="{{ $params['view_type'] ?? 'popup' }}">

            @if (!empty($params['return_url']))
                <input type="hidden" name="return_url" value="{{ $params['return_url'] }}">
            @endif

            @if (!empty($params['cancel_url']))
                <input type="hidden" name="cancel_url" value="{{ $params['cancel_url'] }}">
            @endif

            @if (!empty($params['continue_success_url']))
                <input type="hidden" name="continue_success_url" value="{{ $params['continue_success_url'] }}">
            @endif

            @if (!empty($params['return_params']))
                <input type="hidden" name="return_params" value="{{ $params['return_params'] }}">
            @endif

            <input type="hidden" name="hash" value="{{ $params['hash'] }}" id="hash">
            <div id="checkout_fallback" style="display: none; width: 100%; text-align: end; margin-top: 5px;">
                <input type="button" id="checkout_button" value="Checkout Now">
            </div>
        </form>
    </div>

    <script src="https://checkout.payway.com.kh/plugins/checkout2-0.js"></script>

    <script>
        $(document).ready(function(){
            function openAbaCheckout() {
                $('#aba_merchant_request').append($(".payment_option:checked"));
                AbaPayway.checkout();
            }

            $('#checkout_button').click(openAbaCheckout);

            var startedAt = Date.now();
            var waitForPayWay = setInterval(function () {
                if (typeof AbaPayway !== 'undefined' && typeof AbaPayway.checkout === 'function') {
                    clearInterval(waitForPayWay);
                    openAbaCheckout();
                }

                if (Date.now() - startedAt > 6000) {
                    clearInterval(waitForPayWay);
                    $('#checkout_fallback').show();
                }
            }, 100);
        });
    </script>
</body>
</html>
