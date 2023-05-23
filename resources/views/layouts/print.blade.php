<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Invoice {{ Route::current()->parameter('id') }} - {{ config('app.name', 'Sri Sri Tattva') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,500;0,600;0,800&display=swap"
    rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('svg/connection.svg') }}" />
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div>
        <main id="app" class="p-0">
            <vue-progress-bar></vue-progress-bar>
            @yield('content')
        </main>
    </div>
    <script src="{{ asset('js/manifest.js') }}" defer></script>
    <script src="{{ asset('js/vendor.js') }}" defer></script>
    <script src="{{ asset('js/app.js') }}" defer></script>
</body>
</html>
