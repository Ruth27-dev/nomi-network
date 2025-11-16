@extends('admin::auth.index')
@section('auth')
    <form class="form" x-data="loginForm" action="{{ route('admin-login-post') }}" method="POST" autocomplete="off">
        @csrf
        <input type="hidden" name="redirect" value={{ request()->redirect }}>
        <p class="welcome !text-gray-400">Welcome back!!!</p>
        <h2 class="title p-0 !text-gray-600">Sign In</h2>
        <div class="form-wrapper">
            <div style="display: none">
                <input type="password" name="" id="">
            </div>
            <div class="form-row">
                <label for="username">
                    <span class="material-icons-outlined text-gray-400">email</span>
                    <span class="text-gray-400 ml-2">Email <span class="text-red-500">*</span></span>
                </label>
                <input name="email" placeholder="Enter email" type="text" autocomplete="off" readonly onfocus="this.removeAttribute('readonly');">
                @error('email')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-row">
                <label for="password">
                    <span class="material-icons-outlined text-gray-400">key</span>
                    <span class="text-gray-400 ml-2">Password <span class="text-red-500">*</span></span>
                </label>
                <div class="group-input" x-data={show:false}>
                    <input name="password" x-bind:type="!show ? 'password' : 'text'" placeholder="Enter password"
                        error-message="Incorrect password" autocomplete="off" readonly onfocus="this.removeAttribute('readonly');">
                    <div class="group-item" @click="show = !show">
                        <span x-show="show">
                            <span class="material-icons-outlined text-gray-400">
                                visibility</span>
                        </span>
                        <span x-show="!show">
                            <span class="material-icons-outlined text-gray-400">visibility_off</span>
                        </span>
                    </div>
                </div>
                @error('password')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>
            <div class="option">
                <div class="remember-me">
                    <input class="w-5 h-5 cursor-pointer" name="remember" id="remember" type="checkbox" value="1">
                    <label for="remember" class="text-gray-400 ml-2 cursor-pointer">Remember</label>
                </div>
            </div>
            <button class="btn-submit-form text-white" type="submit">
                <span class="font-bold">Sign In</span>
            </button>
        </div>
        @if (Session::has('message'))
            <p class="q-label-error">{{ Session::get('message') }}</p>
        @endif
        @if (Session::has('error'))
            <p class="q-label-error">{{ Session::get('error') }}</p>
        @endif
    </form>
@stop
@section('script')
    <script type="module">
        Alpine.data('loginForm', () => ({
            init() {
                
            }
        }));
    </script>
@stop
