@php
    // Adds impersonating class if the user is impersonating
    if( session( 'login.loginType') == "private-login" ){
        $impersonating = "impersonating";
    }else{
        $impersonating = "";
    }
@endphp
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('app.layouts.head')
    </head>
    <body id="pm" class="app-core m-0 p-0 {{ $impersonating }}">
        <header class="container-fluid bg-navy-blue">
            @include('app.layouts.navbar')
        </header>
        <main class="container-fluid mb-4">
            <div class="row">
                <div class="col-12 col-md-3 col-lg-3 col-xl-3 px-0 mb-5 d-none d-md-block">
                    @include('app.layouts.sidebar')
                </div>
                <div class="col-12 col-md-9 col-lg-9 col-xl-9 px-3 py-4">
                    @yield('content','')
                </div>
            </div>
        </main>
        <footer class="container-fluid bg-gradient-blue mt-5">
            @include('app.layouts.footer')
        </footer>
    </body>
</html>
