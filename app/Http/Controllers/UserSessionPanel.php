<?php


namespace App\Http\Controllers;

use App\Http\Middleware\PMWShandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class UserSessionPanel
{
    /**
     * Authenticate URL against Webservices then redirect it to its home page
     *
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    private $PMWShandler;
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->PMWShandler = new PMWShandler();
    }

    public function isLogged()
    {
        if( $this->request->session()->has('login.loggedSince') &&
            $this->request->session()->get('login.loggedSince') > 1000000 &&
            $this->request->session()->get('login.loginType') == "private-login" ){

            return true;
        }else{
            $this->backToHome();
        }
    }

    function backToHome(){
        header("Location: " . config('filesystems.disks.panel.login') );
        die();
    }

    function logout()
    {
        app('debugbar')->disable();

        // clears session
        $this->request->session()->forget('login');
        Auth::logout();
        Session::flush();

        // Clears cookies
        if (isset($_SERVER['HTTP_COOKIE'])) {
            $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
            foreach($cookies as $cookie) {
                $parts = explode('=', $cookie);
                $name = trim($parts[0]);
                setcookie($name, '', time()-1000);
                setcookie($name, '', time()-1000, '/');
            }
        }

        $this->backToHome();
    }

}
