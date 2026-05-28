<?php

use App\Http\Controllers\PayWayWebController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;

// PayWay checkout page — opened in mobile WebView
Route::get('/payway/checkout/{tranId}', [PayWayWebController::class, 'checkout'])
    ->name('payway.checkout');

Route::get('/login', [UserController::class, 'login'])->name('login');
Route::get('/', function () {
    if (auth()->guard('admin')->check()) {
        return redirect()->route('admin-user-list');
    }
    return redirect()->route('admin-login');
});