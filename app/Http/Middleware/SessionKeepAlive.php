<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;

class SessionKeepAlive
{
    public function handle($request, Closure $next)
    {
        // Perbarui timestamp di session setiap request
        Session::put('last_activity', now());

        return $next($request);
    }
}
