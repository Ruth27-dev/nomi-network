<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LITTLE DUCKLING @yield('title')</title>
    <link rel="shortcut icon" href="{!! asset('images/navigator.png') !!}" type="image/x-icon">
    <style>
        #toast-container>div {
            width: 400px !important;
            font-size: 14px !important;
        }
    </style>
    {{-- Load Vite JS + CSS EARLY --}}
    @vite(['resources/admin/sass/app.scss', 'resources/admin/css/app.css', 'resources/admin/js/app.js', 'resources/admin/js/body.js'])
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    {{-- Delayed inline JS that depends on jQuery/Toastr --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if (Session::has('success'))
                toastr.success("{!! Session::get('success') !!}", "Success Message!", {
                    progressBar: true,
                    timeOut: 5000
                });
            @elseif (Session::has('error'))
                toastr.error("{!! Session::get('error') !!}", "Error Message!", {
                    progressBar: true,
                    timeOut: 5000
                });
            @elseif (Session::has('warning'))
                toastr.warning("{!! Session::get('warning') !!}", "Warning Message!", {
                    progressBar: true,
                    timeOut: 5000
                });
            @endif
        });
    </script>

    @yield('style')
</head>

<body>
    @yield('index')
    @yield('script')

    <script>
        const appConfig = {!! json_encode([
            'status' => [
                'active' => config('dummy.status.active.key'),
                'inactive' => config('dummy.status.inactive.key'),
            ],
            'discount' => config('dummy.discount'),
            'stock_inventory' => config('dummy.stock_inventory'),
            'locale' => [
                'en' => config('dummy.locale.en'),
                'km' => config('dummy.locale.km'),
                'zh' => config('dummy.locale.zh'),
            ],
            'langLocale' => app()->getLocale(),
            'order' => config('dummy.order'),
            'lov' => config('dummy.lov.type'),
            'user' => config('dummy.user')
        ]) !!}



        let active = appConfig.status.active;
        let inactive = appConfig.status.inactive;

        let langLocale = appConfig.langLocale;
        let arrayLangLocale = {
            en: appConfig.locale.en,
            km: appConfig.locale.km,
        };
    </script>
</body>

</html>
