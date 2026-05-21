<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException; // ← add this

class JwtMiddleware
{
    public function handle($request, Closure $next)
    {

        try {
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return response()->json([
                    'status'  => false,
                    'message' => 'User not found',
                ], 401);
            }
        } catch (TokenExpiredException $e) {        // ← add this
            return response()->json([
                'status'  => false,
                'message' => 'Token has expired.',
            ], 401);
        } catch (TokenInvalidException $e) {        // ← add this
            return response()->json([
                'status'  => false,
                'message' => 'Token is invalid.',
            ], 401);
        } catch (UnauthorizedHttpException $e) {    // ← add this
            return response()->json([
                'status'  => false,
                'message' => 'Token not provided.',
            ], 401);
        } catch (JWTException $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Token Invalid',
            ], 401);
        }

        return $next($request);
    }
}
