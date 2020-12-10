<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('app.layouts.head')
</head>
<body>
    @include('app.layouts.navbar')
    @include('app.layouts.menu')
    @yield('content','')
    @include('app.layouts.footer')
</body>
</html>
