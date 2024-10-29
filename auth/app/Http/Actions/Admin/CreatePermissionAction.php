<?php

namespace App\Http\Actions\Admin;

use App\Http\Requests\CreatePermissionRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Permission;

class CreatePermissionAction
{
    public function handle(CreatePermissionRequest $request): JsonResponse
    {
        try {
            if (Permission::query()->updateOrCreate([...$request->validated(), 'guard_name' => 'admin']))
                return successfulJsonResponse(data: ['permissions' => Permission::query()->pluck('name')], message: 'Available Permissions');
        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
