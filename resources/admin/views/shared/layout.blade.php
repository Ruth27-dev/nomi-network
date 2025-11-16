@extends('admin::index')
@section('index')
    <div class="container" x-data="{ sidebarOpen: true }">
        <div class="container-wrapper">
            <div class="sidebar" :class="{ 'ml-[-270px]': !sidebarOpen }" style="background-color: #fff;">
                @include('admin::shared.sidebar')
            </div>
            <div class="content" x-data="{}">
                @yield('layout')
                @include('admin::components.confirm-dialog')
                @include('admin::components.toast')
                @include('admin::components.select-option')
                @include('admin::components.add-map')
                @include('admin::pages.profile.change-password')
                @include('admin::pages.profile.store')
                @include('admin::components.view-invoice')
            </div>
        </div>
    </div>
@stop
