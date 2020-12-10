@php
    app('debugbar')->disable();
    header('Access-Control-Allow-Origin: *');
    header("Content-Type: application/javascript; charset: UTF-8");

    echo file_get_contents( getcwd() . "/js/iframe.js");
@endphp
