<?php

namespace App\Http\Actions\Admin;

use App\Http\Requests\AdminLoginRequest;
use App\Http\Resources\AdminResource;
use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class AdminLoginAction
{
    public function handle(AdminLoginRequest $request): JsonResponse
    {
        try {
            logger()->info("### AUTHENTICATING ADMIN ###");
            logger($request->except('password'));

            if ($token = auth('admin')->attempt($request->validated())) {
                logger($admin = auth('admin')->user());

                $admin->bearer_token = $token;
                return successfulJsonResponse(data: new AdminResource($admin), message: 'Admin authenticated');
            }
            return errorJsonResponse(
                errors: ['Invalid credentials'],
                message: 'Authentication failed',
                statusCode: ResponseAlias::HTTP_UNAUTHORIZED);
            
        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
