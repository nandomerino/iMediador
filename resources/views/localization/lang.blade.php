@php
    App::setLocale('es');
    app('debugbar')->disable();

    /**
    * loads currently used language file on JS to use its strings there
    **/
    $locale = App::getLocale();
    $data = file_get_contents( resource_path() .'/lang/' . $locale . '.json' );

    header('Content-Type: application/javascript');
    echo "var lang = " . $data;

@endphp
