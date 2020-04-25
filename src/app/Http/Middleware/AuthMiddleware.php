<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\SignatureInvalidException;

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
        try
        {
            $tokenPayload = JWT::decode($request->bearerToken(), env('JWT_PRIVATE_KEY'), ['HS256']);
        }
        catch (SignatureInvalidException $e)
        {
            return response()->json(['error' => 'TOKEN_INVALID'], 401);
        }
        catch (BeforeValidException $e)
        {
            return response()->json(['error' => 'TOKEN_INVALID'], 401);
        }
        catch (ExpiredException $e)
        {
            return response()->json(['error' => 'TOKEN_EXPIRED'], 401);
        }
        catch (\UnexpectedValueException $e)
        {
            return response()->json(['error' => 'TOKEN_INVALID'], 401);
        }

        $request->attributes->add(['jwtPayload' => $tokenPayload]);

        return $next($request);
    }
}
