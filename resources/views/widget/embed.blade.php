@php
    header('Access-Control-Allow-Origin: *');
@endphp


<div id="pm" class="container my-4 app-core">
    <div class="py-4 w-100">
        @include('widget.layouts.content')
        @include('widget.layouts.modal')
    </div>
</div>
@include('widget.layouts.head-mini')


<?php
    /*
$domain = "https://suitaprest-form.wldev.es";

// CSS
$embed = "<style>";
$embed .= file_get_contents( $domain .'/css/style.css' );
$embed .= "</style>";

// HTML
$embed .= file_get_contents( $domain .'/index.html' );

// JS
$embed .= "<script>";
$embed .= file_get_contents( $domain .'/js/custom.js' );
$embed .= "</script>";

header('Access-Control-Allow-Origin: *');

echo $embed;
