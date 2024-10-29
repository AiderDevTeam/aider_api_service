<?php

namespace App\Http\Actions\Admin;

use App\Http\Requests\PermissionsRequest;
use App\Models\Admin;
use Exception;

class AssignPermissionsAction
{
    public function handle(Admin $admin, PermissionsRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $admin->givePermissionTo($request->validated('permissions'));
            return successfulJsonResponse(['permissions'=>$admin->getAllPermissions()->pluck('name')]);
        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
