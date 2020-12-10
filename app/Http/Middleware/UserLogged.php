<?php

namespace App\Http\Middleware;

use App\Http\Controllers\UserSession;
use Illuminate\Http\Request;
use Closure;

class UserLogged
{
    public function handle($request, Closure $next)
    {
        // Perform action
        $UserSession = new UserSession($request);
        $UserSession->isLogged();

        return $next($request);
    }

}
