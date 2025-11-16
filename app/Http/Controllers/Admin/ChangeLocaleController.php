<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ChangeLocaleController extends Controller
{
    public function changeLocale(Request $req)
    {
        Session::put('locale', $req->locale);
        return redirect()->back();
    }
}
