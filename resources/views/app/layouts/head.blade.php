<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ config('app.name') }} @php if( isset($title) ){ echo "- ".$title; } @endphp</title>

<!-- Styles -->
<link href="/WP/wp-content/themes/Avada/assets/css/style.min.css" rel="stylesheet">
<link href="/WP/wp-content/themes/Avada-Child-Theme/style.css" rel="stylesheet">
{{-- Loads WP dinamically generated CSS --}}
@php
    // Sometimes Wordpress hasn't generated these files so we load a page to force it to load them
    if ( !file_exists( config('filesystems.disks.local.WPcss') ) ){
        $curl = curl_init( config('filesystems.disks.public.urlCheck') );
        curl_exec($curl);
    }
    // ----------------------------
    foreach(scandir( config('filesystems.disks.local.WPcss') ) as $cssfile){
        if( $cssfile != "." && $cssfile != ".." ){
            $CSSFilename = $cssfile;
        }
    }
    if( $CSSFilename ){
        echo "<link href='".config('filesystems.disks.public.WPcss').$CSSFilename."' rel='stylesheet'>";
    }
@endphp
<link href="/css/bootstrap-4.3.1.min.css" rel="stylesheet">
<link href="/css/fontawesome-free-5.11.2.min.css" rel="stylesheet">
<link href="/css/custom.css?v=2" rel="stylesheet">
<link href="/css/jquery-ui.min.css" rel="stylesheet">

<!-- Scripts -->
<!--
<script src="/js/jquery-3.3.1.slim.min.js"></script>
<script src="/WP/wp-content/plugins/contact-form-7/includes/js/scripts.js"></script>
<script src="/js/jquery.form-validator.min.js"></script>-->

<script src="/WP/wp-includes/js/jquery/jquery.js"></script>
<script src="/WP/wp-includes/js/jquery/jquery-migrate.min.js"></script>

<script src="/js/popper-1.14.7.min.js"></script>
<script src="/js/bootstrap-4.3.1.min.js"></script>
<script src="/js/fontawesome-free-5.11.2.min.js"></script>
<script src="/js/jquery.form.min.js"></script>
<script src="/js/jquery-ui.min.js"></script>
<script src="/js/datepicker-es.js"></script>

{{-- Loads WP dinamically generated JS --}}
@php
    foreach(scandir( config('filesystems.disks.local.WPjs') ) as $jsfile){
        if( $jsfile != "." && $jsfile != ".." ){
            $JSFilename = $jsfile;
        }
    }
    if( $JSFilename ){
        echo "<script src='".config('filesystems.disks.public.WPjs').$JSFilename."'></script>";
    }
@endphp

<script src="/lang.js?v=10"></script>
<script src="/js/custom.js?v=12"></script>
<script src="/js/advisor-data.js?v=1"></script>
<script src="/js/iban.js"></script>
