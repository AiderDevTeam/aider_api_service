<?php

namespace App\Http\Actions\Admin;

use App\Http\Requests\CreateRoleRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Role;

class CreateRoleAction
{
    public function handle(CreateRoleRequest $request): JsonResponse
    {
        try {
            if (Role::query()->updateOrCreate([...$request->validated(), 'guard_name' => 'admin']))
                return successfulJsonResponse(data: ['roles' => Role::query()->pluck('name')], message: 'Available Roles');
        } catch (Exception $exception) {
            request($exception);
        }
        return errorJsonResponse();
    }
}
