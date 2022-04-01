@php
    use App\Http\Middleware\PMWShandler;
    App::setLocale('es');
    app('debugbar')->disable();

    $PMWShandler = new PMWShandler();

    switch( $_GET["downloadType"] ){
        case "test":

            //echo "<pre>";

            $_GET["productor"] = 130810005;
            $_GET["docId"] = 2020070035;
            $_GET["source"] = 2;
            $_GET["type"] = "SO";
            $_GET["format"] = "A4";
/*

            $_GET["productor"] = 1200364;
            $_GET["docId"] = 582793;
            $_GET["source"] = 1;
            $_GET["type"] = "DU";
            $_GET["format"] = "A4";
*/
            // Call PM WS
            $data = $PMWShandler->getDocument(
                $_GET["productor"],
                $_GET["docId"],
                $_GET["source"],
                $_GET["type"],
                $_GET["format"]
            );

/*
            var_dump($data);


            $_GET["fileId"] = 326568;

            $data = $PMWShandler->getFile(
                $_GET["fileId"]
            );

            var_dump($data);

            echo "</pre>";
*/
           if (is_array($data)) {


                // $base64 = base64_encode( file_get_contents( config('filesystems.disks.local.downloads') . "dummy.pdf") );

                // $base64 = trim( preg_replace( '/\s\s+/', ' ',  $data['base64'] ) );

                $decoded = base64_decode( $data['contenidoFichero'] );
                // $file = 'dummy.pdf';
                //$file = $_GET["docId"] . '.pdf';
                $file = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'file-' . time() . '.pdf';
                $r = file_put_contents($file, $decoded);

                if (file_exists($file)) {
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename="' . basename($file) . '"');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate');
                    header('Pragma: public');
                    header('Content-Length: ' . filesize($file) );
                    readfile($file);
                    unlink($file);
                }

            }

            break;

        case "document":

            // Call PM WS
            $data = $PMWShandler->getDocument(
                $_GET["productor"],
                $_GET["docId"],
                $_GET["source"],
                $_GET["type"],
                $_GET["format"]
            );
            switch ($_GET["type"]) {
                case "SO":
                    $name = "Solicitud";
                    break;
                case "CP":
                    $name = "Condiciones-Particulares";
                    break;

                case "CG":
                    $name = "Condiciones-generales";
                    break;

                case "REC":
                    $name = "Recibo";
                    break;
            }

            $decoded = base64_decode( $data['contenidoFichero'] );
            $file = $name.'-'.$_GET["docId"] . '.pdf';
            $r = file_put_contents($file, $decoded);

            if (file_exists($file)) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . basename($file) . '"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($file) );
                readfile($file);
                unlink($file);
            }

            break;

        case "file":

            $data = $PMWShandler->getFile(
                $_GET["fileId"]
            );

            $file = $_GET["filename"];
            $contenidoFichero = $data['contenidoFichero'];

            if( $_GET['tipoFichero'] == 2 || $_GET['tipoFichero'] == 6 ){
                $contenidoFichero = base64_decode( $contenidoFichero );
            }

            $r = file_put_contents($file, $contenidoFichero);

            if (file_exists($file)) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . basename($file) . '"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($file) );
                readfile($file);
                unlink($file);
            }
            break;

        case "attachment":

            $data = $PMWShandler->getFile( $_GET["fileId"] );
            $contenidoFichero = $data['contenidoFichero'];

            if( $_GET['tipoFichero'] == 2 || $_GET['tipoFichero'] == 6 ){
                $contenidoFichero = base64_decode( $contenidoFichero );
            }

            $filePath = config("filesystems.disks.local.downloads") . $_GET["filename"];
            $r = file_put_contents($filePath, $contenidoFichero);

            echo $filePath;

            break;



    }



@endphp
