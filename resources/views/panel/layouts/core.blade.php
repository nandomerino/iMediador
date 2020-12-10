<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('panel.layouts.head')
    </head>
    <body id="panel" class="app-core m-0 p-0">
        <header class="container-fluid bg-lime-yellow">
            @include('panel.layouts.navbar')
        </header>
        <main class="container-fluid mb-4">
            <div class="row">
                <div class="col-12 col-md-3 col-lg-3 col-xl-3 px-0 mb-5 d-none d-md-block">
                    @include('panel.layouts.sidebar')
                </div>
                <div class="col-12 col-md-9 col-lg-9 col-xl-9 px-3 py-4">
                    @yield('content','')
                </div>
            </div>
        </main>
        <footer class="container-fluid bg-gradient-blue mt-5">
            @include('panel.layouts.footer')
        </footer>
    </body>
</html>
