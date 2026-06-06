<!-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>Processing Payment...</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: #f5f5f5;
            font-family: sans-serif;
        }
        .spinner {
            width: 48px; height: 48px;
            border: 5px solid #e0e0e0;
            border-top-color: #00a651;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin-bottom: 16px;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        p { color: #555; font-size: 15px; }
    </style>
</head>
<body>

    <div class="spinner"></div>
    <p>Redirecting to payment...</p>

    {{-- Auto-submit form to ABA PayWay --}}
    <form id="payway_form" method="POST" action="{{ $params['api_url'] }}">
        <input type="hidden" name="hash"           value="{{ $params['hash'] }}">
        <input type="hidden" name="tran_id"        value="{{ $params['tran_id'] }}">
        <input type="hidden" name="amount"         value="{{ $params['amount'] }}">
        <input type="hidden" name="firstname"      value="{{ $params['firstname'] }}">
        <input type="hidden" name="lastname"       value="{{ $params['lastname'] }}">
        <input type="hidden" name="email"          value="{{ $params['email'] }}">
        <input type="hidden" name="phone"          value="{{ $params['phone'] }}">
        <input type="hidden" name="req_time"       value="{{ $params['req_time'] }}">
        <input type="hidden" name="merchant_id"    value="{{ $params['merchant_id'] }}">
        <input type="hidden" name="payment_option" value="{{ $params['payment_option'] }}">
    </form>

    <script>
        document.getElementById('payway_form').submit();
    </script>

</body>
</html> -->



    <div class="container" style="height: 80vh;">
        <form id="aba_merchant_request" target="aba_webservice" action="{{ PayWayApiCheckout::getApiUrl() }}" method="POST" class="right" style="flex-direction: column; align-items: flex-start;">
            {{ csrf_field() }}
            <input type="hidden" name="req_time" value="{{ $req_time }}">
            <input type="hidden" name="merchant_id" value="{{ $merchant_id }}">
            <input type="hidden" name="tran_id" value="{{ $tran_id }}" id="tran_id">
            <input type="hidden" name="amount" value="{{ $amount }}" id="amount">
            <input type="hidden" name="items" value="{{ $items }}">
            <input type="hidden" name="firstname" value="{{ $firstname }}">
            <input type="hidden" name="lastname" value="{{ $lastname }}">
            <input type="hidden" name="email" value="{{ $email }}">
            <input type="hidden" name="phone" value="{{ $phone }}">
            <input type="hidden" name="payment_option" value="{{ $payment_option }}">
            <input type="hidden" name="return_url" value="{{ $return_url }}">
            <input type="hidden" name="cancel_url" value="{{ $cancel_url }}">
            <input type="hidden" name="continue_success_url" value="{{ $continue_success_url }}">
            <input type="hidden" name="return_params" value="{{ $return_params }}">

            <input type="hidden" name="hash" value="{{ $hash }}" id="hash">
            <div style="width: 100%; text-align: end; margin-top: 5px;">
                <input type="button" id="checkout_button" value="Checkout Now">
            </div>
        </form>
    </div>

    <script src="https://checkout.payway.com.kh/plugins/checkout2-0.js"></script>

    <script>
        $(document).ready(function(){
            $('#checkout_button').click(function(){
                AbaPayway.checkout();
            });
        });
    </script>

