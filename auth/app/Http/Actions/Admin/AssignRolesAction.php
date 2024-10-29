<?php

namespace App\Http\Actions\Admin;

use App\Http\Requests\RolesRequest;
use App\Models\Admin;
use Exception;
use Illuminate\Http\JsonResponse;

class AssignRolesAction
{
    public function handle(Admin $admin, RolesRequest $request): JsonResponse
    {
        try {
            $admin->assignRole($request->validated('roles'));
            return successfulJsonResponse(['roles' => $admin->roles()->pluck('name')]);
        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
