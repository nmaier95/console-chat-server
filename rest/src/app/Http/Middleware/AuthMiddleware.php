<?php

namespace App\Http\Middleware;

use Closure;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if($request->bearerToken() !== $_ENV['API_TOKEN']) {
            return response()->json('No or invalid token provided', 401);
        }

        return $next($request);
    }
}
