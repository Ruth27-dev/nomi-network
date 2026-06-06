
    <div class="container" style="height: 80vh;">
        <form id="aba_merchant_request" target="aba_webservice" action="{{ App\Http\ABA\PayWayApiCheckout::getApiUrl() }}" method="POST" class="right" style="flex-direction: column; align-items: flex-start;">
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
            <input type="hidden" name="view_type" value="popup">
            <input type="hidden" name="return_url" value="{{ $return_url }}">
            <!-- <input type="hidden" name="cancel_url" value="{{ $cancel_url }}">
            <input type="hidden" name="continue_success_url" value="{{ $continue_success_url }}">
            <input type="hidden" name="return_params" value="{{ $return_params }}"> -->

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
