<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;

Route::get('/login', [UserController::class, 'login'])->name('login');
Route::get('/', function () {
    if (auth()->guard('admin')->check()) {
        return redirect()->route('admin-user-list');
    }
    return redirect()->route('admin-login');
});