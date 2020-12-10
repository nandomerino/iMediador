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
            <div class="py-4 w-100">
                @yield('content','')
            </div>
        </main>
        <footer class="container-fluid bg-gradient-blue mt-5">
            @include('app.layouts.footer')
        </footer>
    </body>
</html>
