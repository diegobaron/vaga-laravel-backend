<?php

namespace App\Http\Middleware;

use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ApiProtectedRoute extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {   
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch(\Exception $e) {
            if($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return response()->json(['message' => 'Token is Invalid'], Response::HTTP_UNAUTHORIZED);
            } else if($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return response()->json(['message' => 'Token is Expired'], Response::HTTP_UNAUTHORIZED);
            } else {
                return response()->json(['message' => 'Authorization Token not found'], Response::HTTP_UNAUTHORIZED);
            }
        }
        return $next($request);
    }
}
