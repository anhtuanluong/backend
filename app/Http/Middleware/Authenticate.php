<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    public function handle($request, \Closure $next, ...$guards)
    {
        if ($this->authenticate($request, $guards) === 'authentication_failed') {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        return $next($request);
    }
}
