@php
header('P3P: CP="CAO PSA OUR"');
session_start();
@endphp
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('widget.layouts.head')
</head>
<body id="pm" class="app-core m-0 p-0">
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-M5K2GQQ"
                  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
{{--<header style="display: none;">
    @include('widget.layouts.header')
</header>--}}
<main class="container">
    <div class="py-4 w-100">
        @include('widget.layouts.content')
        @include('widget.layouts.modal')
    </div>
</main>
{{--<footer style="display: none;">
    @include('widget.layouts.footer')
</footer>--}}
</body>
</html>
