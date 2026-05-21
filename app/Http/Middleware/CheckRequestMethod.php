<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRequestMethod
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $method): Response
    {
        if ($request->method() !== strtoupper($method)) {
            return response()->json([
                'status'  => false,
                'message' => 'Method not allowed.',
                'allowed' => strtoupper($method),
                'current' => $request->method(),
            ], 405);
        }

        return $next($request);
    }
}
