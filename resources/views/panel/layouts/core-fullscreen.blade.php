<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('panel.layouts.head')
    </head>
    <body id="panel" class="panel-core login m-0 p-0">
        <main class="container mb-4">
            @yield('content','')
        </main>
    </body>
</html>
