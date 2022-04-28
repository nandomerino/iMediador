<?php

namespace App\Http\Controllers;

use DebugBar\DebugBar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\Http\Controllers\Controller;
use App\Http\Middleware\PMWShandler;

class FormController extends Controller
{

    public function loginForm(Request $request)
    {
        // Gets form variables
        $input = $request->all();
        // URL PRIVATE LOGIN
        // https://imediador.wldev.es/url-login?user=6600031&pass=G7742&gestor=&userPM=451&loginType=private-login&action=urlLogin

        // Call PM WS
        $PMWShandler = new PMWShandler();
        switch( $input["loginType"] ){
            case "app-login":
                app('debugbar')->info('$input app-login');
                app('debugbar')->info($input);
                $data = $PMWShandler->login($input["user"], $input["pass"], $input["gestor"], $input["loginType"], $input["action"], $input["entryChannel"]);
                app('debugbar')->info('$data app-login');
                app('debugbar')->info($data);
                if( $data === true) {
                    return response()->json(['success'=> true, 'redirect'=> config('filesystems.disks.app.home') ]);
                }else {
                    $request->session()->flush();
                    return response()->json(['success'=> false, 'e'=> $data]);
                }
                break;

            case "recovery-login":

                $pass = 'null';
                $data = $PMWShandler->login($input["user"], $pass, $input["gestor"], $input["loginType"], $input["action"], $input["entryChannel"]);
                app('debugbar')->info('$data recovery login formcontroller');
                app('debugbar')->info($data);
                if( $data === true) {
                    return response()->json(['success'=> true, 'redirect'=> config('filesystems.disks.app.home') ]);
                }else {
                    $request->session()->flush();
                    return response()->json(['success'=> false, 'e'=> $data]);
                }
                break;

            case "private-login":
                $data = $PMWShandler->login($input["user"], $input["pass"], $input["gestor"], $input["loginType"], $input["action"], $input["userPM"], $input["entryChannel"]);
                if( $data === true) {
                    if( $input["action"] == "urlLogin"){
                        header("Location: " . config('filesystems.disks.panel.home') );
                        die();
                    }else{
                        return response()->json(['success'=> true, 'redirect'=> config('filesystems.disks.panel.home') ]);
                    }

                }else {
                    $request->session()->flush();
                    return response()->json(['success'=> false, 'e'=> $data]);
                }
                break;
        }


    }

}
