<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response) $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($bearerToken = $request->bearerToken()) {
            $_request = Http::withHeaders(jsonHttpHeaders())->withToken($bearerToken)->get('auth/api/user');
            if ($_request->successful()) {
                $request->user = $_request->json('data');
            } else {
                return errorJsonResponse(message: 'User authorization failed', statusCode: 401);
            }
        } else {
            return errorJsonResponse(message: 'Authorization header not set', statusCode: 401);
        }
        return $next($request);
    }
}
