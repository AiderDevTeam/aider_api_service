<?php

namespace App\Http\Controllers;

use App\Http\Actions\Admin\AdminLoginAction;
use App\Http\Actions\Admin\AdminUpdateUserAction;
use App\Http\Actions\Admin\ApproveKYCAction;
use App\Http\Actions\Admin\AssignPermissionsAction;
use App\Http\Actions\Admin\AssignRolesAction;
use App\Http\Actions\Admin\CreatePermissionAction;
use App\Http\Actions\Admin\CreateRoleAction;
use App\Http\Actions\Admin\RevokePermissionsAction;
use App\Http\Actions\Admin\RevokeRolesAction;
use App\Http\Actions\Admin\StoreAdminAction;
use App\Http\Actions\User\FetchUsersAction;
use App\Http\Actions\User\LogoutAction;
use App\Http\Requests\AdminLoginRequest;
use App\Http\Requests\CreatePermissionRequest;
use App\Http\Requests\CreateRoleRequest;
use App\Http\Requests\KYCApprovalRequest;
use App\Http\Requests\PermissionsRequest;
use App\Http\Requests\RolesRequest;
use App\Http\Requests\StoreAdminRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\AdminResource;
use App\Models\Admin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    public function index(): JsonResponse
    {
        return successfulJsonResponse(new AdminResource(auth()->user()));
    }

    public function store(StoreAdminRequest $request, StoreAdminAction $action): JsonResponse
    {
        return $action->handle($request);
    }

    public function show(Admin $admin): JsonResponse
    {
        return successfulJsonResponse([new AdminResource($admin)]);
    }

    public function update(Request $request, Admin $admin)
    {
        //
    }

    public function login(AdminLoginRequest $request, AdminLoginAction $action): JsonResponse
    {
        return $action->handle($request);
    }

    public function logout(LogoutAction $action): JsonResponse
    {
        return $action->handle();
    }

    public function createPermission(CreatePermissionRequest $request, CreatePermissionAction $action): JsonResponse
    {
        return $action->handle($request);
    }

    public function createRole(CreateRoleRequest $request, CreateRoleAction $action): JsonResponse
    {
        return $action->handle($request);
    }

    public function assignRole(Admin $admin, RolesRequest $request, AssignRolesAction $action): JsonResponse
    {
        return $action->handle($admin, $request);
    }

    public function revokeRole(Admin $admin, RolesRequest $request, RevokeRolesAction $action): JsonResponse
    {
        return $action->handle($admin, $request);
    }

    public function assignPermission(Admin $admin, PermissionsRequest $request, AssignPermissionsAction $action): JsonResponse
    {
        return $action->handle($admin, $request);
    }

    public function revokePermission(Admin $admin, PermissionsRequest $request, RevokePermissionsAction $action): JsonResponse
    {
        return $action->handle($admin, $request);
    }

    public function getRoles(): JsonResponse
    {
        return successfulJsonResponse(data: ['roles' => Role::query()->pluck('name')], message: 'Available Roles');
    }

    public function getPermissions(): JsonResponse
    {
        return successfulJsonResponse(data: ['permissions' => Permission::query()->pluck('name')], message: 'Available Permissions');
    }

    public function users(Request $request, FetchUsersAction $action): JsonResponse
    {
        return $action->handle($request);
    }

    public function updateUser($username, UpdateUserRequest $request, AdminUpdateUserAction $action): JsonResponse
    {
        return $action->handle($username, $request);
    }

    public function verifyUserId(KYCApprovalRequest $request, ApproveKYCAction $action): JsonResponse
    {
        return $action->handle($request);
    }

}
