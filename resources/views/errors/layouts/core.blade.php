<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('errors.layouts.head')
    </head>
    <body id="pm" class="app-core m-0 p-0">
        <main class="container my-4">
            <div class="row">
                <div class="col py-5">
                    @yield('content','')
                </div>
            </div>
        </main>
        <footer class="container-fluid bg-gradient-blue mt-5">
            @include('errors.layouts.footer')
        </footer>
    </body>
</html>
