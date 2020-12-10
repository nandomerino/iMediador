<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('public.layouts.head')
    </head>
    <body id="pm" class="public-core m-0 p-0">
        <header class="container-fluid bg-navy-blue">
            @include('public.layouts.navbar')
        </header>
        <main class="container-fluid mb-4">
            <div class="row">
                <div class="col-12 col-md-9 col-lg-9 col-xl-9">
                    @yield('content','')
                </div>
                <div class="col-12 col-md-3 col-lg-3 col-xl-3 px-0 mb-5">
                    @include('public.layouts.sidebar')
                </div>
            </div>
        </main>
        <footer class="container-fluid bg-gradient-blue mt-5">
            @include('public.layouts.footer')
        </footer>
    </body>
</html>
