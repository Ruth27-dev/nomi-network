<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\Web\CheckCouponResource;
use App\Models\Discount;
use Exception;

class DiscountController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function checkCoupon()
    {
        try {
            $data = Discount::query()
                ->with('conditions', 'products.product')
                ->whereRaw('BINARY `code` = ?', [request('coupon_code')])
                ->where('status', $this->active)
                ->first();

            $message = null;
            return $this->responseSuccess($data ? new CheckCouponResource($data) : [], $message);
        } catch (Exception $e) {
            return $this->responseError();
        }
    }
}
