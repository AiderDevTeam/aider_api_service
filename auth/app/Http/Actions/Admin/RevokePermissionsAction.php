<?php

namespace App\Http\Actions\Admin;

use App\Http\Requests\PermissionsRequest;
use App\Models\Admin;
use Exception;
use Illuminate\Http\JsonResponse;

class RevokePermissionsAction
{
    public function handle(Admin $admin, PermissionsRequest $request): JsonResponse
    {
        try {
            $admin->revokePermissionTo($request->validated('permissions'));
            return successfulJsonResponse(['permissions'=>$admin->getAllPermissions()->pluck('name')]);
        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
