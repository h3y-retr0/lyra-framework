<?php

namespace App\Middlewares;

use Closure;
use Lyra\Auth\Auth;
use Lyra\Http\Middleware;
use Lyra\Http\Request;
use Lyra\Http\Response;

class AuthMiddleware implements Middleware {
    public function handle(Request $request, Closure $next): Response {
        if (Auth::isGuest()) {
            return redirect('/login');
        }

        return $next($request);
    }
}
