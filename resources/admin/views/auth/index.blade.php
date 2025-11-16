@extends('admin::index')
@section('index')
    <div class="login">
        <div class="login-wrapper">
            <div class="bg-image">
                <img class="logo" src="{{ $company?->image ? $company->image_url : asset('images/logo.jpg') }}"
                    alt="Company Logo">
            </div>
            <div class="form-container">
                @yield('auth')
            </div>
        </div>
    </div>
@stop
