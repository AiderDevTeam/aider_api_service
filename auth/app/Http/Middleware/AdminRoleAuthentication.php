<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminRoleAuthentication
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
        if (!auth('admin')->user()->hasRole(Admin::ROLES['ADMIN']))
            return errorJsonResponse(
                errors: ['admin permissions required'],
                message: 'Unauthorized access',
                statusCode: Response::HTTP_UNAUTHORIZED);

        return $next($request);
    }
}
