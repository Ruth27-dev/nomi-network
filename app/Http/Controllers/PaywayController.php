<?php

namespace App\Http\Controllers;

use App\Models\PaywayTransaction;
use App\Services\PayWayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PaywayController extends Controller
{
    public function __construct(private PayWayService $payWay)
    {
    }

    protected array $requiredFields = ['tran_id', 'amount', 'firstname', 'lastname', 'phone'];

    public function index()
    {
        return response()->json([
            'message' => 'PayWay endpoint is ready.',
            'api_url' => $this->payWay->getApiUrl(),
            'merchant_id' => $this->payWay->getMerchantId(),
        ]);
    }

    public function payway_form(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tran_id' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'payment_option' => 'nullable|in:abapay_khqr,cards,abapay_khqr_deeplink',
            'return_url' => 'nullable|url|max:500',
            'cancel_url' => 'nullable|url|max:500',
            'continue_success_url' => 'nullable|url|max:500',
            'return_params' => 'nullable|in:json',
            'order_id' => 'nullable|integer',
            'donation_id' => 'nullable|integer',
            'order_type' => 'nullable|string|max:30',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $tranId = (string) $request->input('tran_id');
        $amount = (string) $request->input('amount');
        $firstname = (string) $request->input('firstname');
        $lastname = (string) $request->input('lastname');
        $phone = (string) $request->input('phone');
        $email = (string) $request->input('email', '');
        $paymentOption = (string) $request->input('payment_option', 'abapay_khqr_deeplink');
        $returnUrl = (string) $request->input('return_url', route('api-web-payway-submit'));
        $cancelUrl = (string) $request->input('cancel_url', config('app.url'));
        $continueSuccessUrl = (string) $request->input('continue_success_url', config('app.url'));
        $returnParams = (string) $request->input('return_params', 'json');

        $params = $this->payWay->buildHostedPurchaseParams(
            $tranId,
            $amount,
            $firstname,
            $lastname,
            $email,
            $phone,
            $paymentOption,
            $returnUrl,
            $cancelUrl,
            $continueSuccessUrl,
            $returnParams
        );

        PaywayTransaction::updateOrCreate(
            ['tran_id' => $tranId],
            [
                'order_id' => $request->input('order_id'),
                'donation_id' => $request->input('donation_id'),
                'tran_type' => $paymentOption,
                'order_type' => (string) $request->input('order_type', 'order'),
                'payment_status' => 'unpaid',
            ]
        );

        return response()->json($this->payWay->purchase($params));
    }

    public function paymentSubmit(Request $request)
    {
        // Compatibility endpoint for legacy payway-submit route.
        // Normal callback processing remains in Api/Web/PaymentController@callback.
        try {
            $tranId = (string) $request->input('tran_id');
            if ($tranId !== '') {
                PaywayTransaction::updateOrCreate(
                    ['tran_id' => $tranId],
                    [
                        'status_code' => (string) $request->input('status_code', ''),
                        'payment_status' => (string) $request->input('payment_status', ''),
                        'raw_callback' => $request->all(),
                    ]
                );
            }
        } catch (\Throwable $e) {
            Log::warning('PaywayController paymentSubmit failed', ['message' => $e->getMessage()]);
        }

        return response()->json(['message' => 'OK']);
    }
}
