<?php

namespace App\Http\Middleware;

use App\Http\Controllers\UserSessionPanel;
use Illuminate\Http\Request;
use Closure;

class UserLoggedPanel
{
    public function handle($request, Closure $next)
    {
        // Perform action
        $UserSession = new UserSessionPanel($request);
        $UserSession->isLogged();

        return $next($request);
    }

}
