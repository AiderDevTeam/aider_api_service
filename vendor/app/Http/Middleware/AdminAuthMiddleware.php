<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($bearerToken = $request->bearerToken()) {
            $authRequest = Http::withToken($bearerToken)->get('http://auth/api/admin');

            if ($authRequest->successful()) {
                $request->admin = $authRequest->json('data');
            } else {
                return errorJsonResponse(message: 'Admin authentication failed', statusCode: \Illuminate\Http\Response::HTTP_UNAUTHORIZED);

            }

        } else {
            return errorJsonResponse(message: 'Authentication header not set', statusCode: \Illuminate\Http\Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
