<?php

namespace App\Http\Actions\Admin;

use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Http\Services\Vendor\ShopService;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class AdminUpdateUserAction
{
    public function handle(string $username, UpdateUserRequest $request): JsonResponse
    {
        logger('### ADMIN UPDATING USER ###', [$username]);
        logger($request);

        try {
            $user = User::findWithUsername($username);

            if (!$user)
                return errorJsonResponse(
                    errors: ['Username is not associated with an account'],
                    message: 'User not found',
                    statusCode: Response::HTTP_UNPROCESSABLE_ENTITY);

            if ($request->filled('username')) {
                if (!$this->shopTagDoesNotExist($username, $request))
                    return errorJsonResponse(['username has been taken'], statusCode: Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            if ($user->update(arrayKeyToSnakeCase($request->except('password')))) {
                logger('### ADMIN USER UPDATE SUCCESSFUL ###');
                $this->updatePersonalShopTag($username, $request);
                return successfulJsonResponse(data: new UserResource($user->refresh()), message: 'User data updated');
            }

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }

    private function shopTagDoesNotExist(string $username, UpdateUserRequest $request): bool
    {
        if ($username === $request->username) return true;
        return (new ShopService(['shopTag' => $request->username]))->checkShopTagExistence();
    }

    private function updatePersonalShopTag(string $username, UpdateUserRequest $request): void
    {
        (new ShopService([
            'shopTag' => $request->username,
            'oldShopTag' => $username
        ]))->updateClosetShopTag();
    }
}
