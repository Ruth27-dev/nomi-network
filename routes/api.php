<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Web as Web;

require_once __DIR__ . '/api/web.php';

Route::prefix('web')->group(function () {
    Route::controller(Web\DiscountController::class)->prefix('discount')->group(function () {
        Route::post('check-coupon', 'checkCoupon');
    });
});
