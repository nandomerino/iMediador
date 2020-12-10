@php
    app('debugbar')->disable();
    header('Access-Control-Allow-Origin: *');
    header("Content-type: text/css; charset: UTF-8");

    echo file_get_contents( getcwd() . "/css/iframe.css");
@endphp
