<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ForgotPasswordRequest;
use App\Http\Requests\Admin\SignInRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;

class AuthController extends Controller
{
    protected $layout = "admin::auth.";

    public function login(SignInRequest $request)
    {
        $remember   = $request->get('remember');
        $credential = User::query()
            ->whereStatus($this->active)
            ->where(function ($query) use ($request) {
                $query->where('email', $request->email)
                    ->orWhere('phone', $request->email);
            })
            ->first();
        if ($credential && Hash::check($request->password, $credential->password) == true) {
            Auth::guard('admin')->login($credential, $remember);
            if (Auth::user()?->status == config('dummy.status.inactive.key')) {
                Auth::guard('admin')->logout();
                return Redirect::back()->with('user_disabled', false);
            }
            return request()->returnUrl ? redirect()->to(request()->returnUrl) : redirect()->route('admin-user-list');
        }
        Auth::guard('admin')->logout();
        return Redirect::back()->with('status', false);
    }

    public function forgotPassword()
    {
        return view($this->layout . "forgot-password");
    }


    public function signOut()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin-login');
    }
}
