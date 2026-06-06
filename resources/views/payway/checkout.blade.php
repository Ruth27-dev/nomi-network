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
<!-- Popup Checkout Form -->
<div id="aba_main_modal" class="aba-modal">
    <!-- Modal content -->
    <div class="aba-modal-content">
        <form method="POST" target="aba_webservice" action="{{ $params['api_url'] }}" id="aba_merchant_request">
            <input type="hidden" name="hash" value="{{ $params['hash'] }}" id="hash"/>
            <input type="hidden" name="tran_id" value="{{ $params['tran_id'] }}" id="tran_id"/>
            <input type="hidden" name="amount" value="{{ $params['amount'] }}" id="amount"/>
            <input type="hidden" name="firstname" value="{{ $params['firstname'] }}"/>
            <input type="hidden" name="lastname" value="{{ $params['lastname'] }}"/>
            <input type="hidden" name="phone" value="{{ $params['phone'] }}"/>
            <input type="hidden" name="email" value="{{ $params['email'] }}"/>
            <input type="hidden" name="req_time" value="{{ $params['req_time'] }}"/>
            <input type="hidden" name="merchant_id" value="{{ $params['merchant_id'] }}"/>

            @if (!empty($params['items']))
                <input type="hidden" name="items" value="{{ $params['items'] }}"/>
            @endif

            @if (!empty($params['view_type']))
                <input type="hidden" name="view_type" value="{{ $params['view_type'] }}"/>
            @endif

            @if (!empty($params['return_url']))
                <input type="hidden" name="return_url" value="{{ $params['return_url'] }}"/>
            @endif

            @if (!empty($params['cancel_url']))
                <input type="hidden" name="cancel_url" value="{{ $params['cancel_url'] }}"/>
            @endif

            @if (!empty($params['continue_success_url']))
                <input type="hidden" name="continue_success_url" value="{{ $params['continue_success_url'] }}"/>
            @endif

            @if (!empty($params['return_params']))
                <input type="hidden" name="return_params" value="{{ $params['return_params'] }}"/>
            @endif
        </form>
    </div>
    <!-- end Modal content -->
</div>
<!-- End Popup Checkout Form -->

<!-- Page Content -->
<div class="container" style="margin-top: 75px;margin: 0 auto;">
    <div style="width: 200px;margin: 0 auto;">
        <div class="wpr_payment_option">
            <div style="margin-top: 10px">
                <input type="radio" name="payment_option" class="payment_option" style="margin: 14px;float: left;" checked value="{{ $params['payment_option'] ?? 'abapay_khqr' }}">
                <label class="paymentOption" for="khqr">
                    <span class="detailCard002" style="margin-left: 16px; float: right; position: absolute;">
                        <strong><span class="titleCard">ABA KHQR</span><br/></strong>
                        <span class="detailCard003" style="margin-top: 5px;">Scan to pay with any banking app</span>
                    </span>
                </label>
            </div>
        </div>
        <h2>TOTAL: {{ $params['amount'] }}</h2>
        <input type="button" id="checkout_button" value="Checkout Now">
    </div>
</div>
<!-- End Page Content -->

<script src="https://checkout.payway.com.kh/plugins/checkout2-0.js"></script>

<script>
    $(document).ready(function(){
        $('#checkout_button').click(function(){
            $('#aba_merchant_request').append($(".payment_option:checked"));
            AbaPayway.checkout();
        });
    });
</script>
<!-- End -->
</body>
</html>
