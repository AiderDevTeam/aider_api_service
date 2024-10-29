<?php

namespace App\Http\Actions\User;

use App\Custom\Status;
use App\Http\Requests\AuthenticationRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class LoginAction
{
    public function handle(AuthenticationRequest $request): JsonResponse
    {
        try {
            logger()->info('### AUTHENTICATING USER ###');
            logger($request->except('password'));

            if (($user = User::findWithEmail($request->validated('email'))) && !$user->isActive()) {
                return errorJsonResponse(
                    errors: ["Account $user->status. Contact customer service"],
                    message: 'Account inactivate',
                    statusCode: Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }

            if ($token = auth()->attempt(['email' => $request->validated('email'), 'password' => $request->validated('password')])) {
                $user = auth()->user();

                $user->updateQuietly([
                    'device_os' => $request->validated('deviceOs'),
                    'push_notification_token' => $request->validated('pushNotificationToken')
                ]);

                $user->token = $token;
                $user->expiresIn = auth()->factory()->getTTL() / 1440 . ' days';

                logger()->info("### USER AUTHENTICATED ###");

                return successfulJsonResponse(
                    new UserResource($user),
                    message: 'User authenticated.'
                );
            }

            logger()->info("### USER AUTHENTICATION FAILED ###");
            return errorJsonResponse(
                message: 'Authentication failed, please check credentials and try again',
                statusCode: Response::HTTP_UNPROCESSABLE_ENTITY
            );
        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }

    private static function validatedEmailOrUsername(AuthenticationRequest $request): array
    {
        if (filter_var($request->validated('username'), FILTER_VALIDATE_EMAIL)) {
            if (!User::findWithEmail($request->validated('username'))) {
                return ['error' => 'Email is not associated with an account'];
            }
            return ['email' => $request->validated('username')];
        }

        if (!($user = User::findWithUsername($request->validated('username')))) {
            return ['error' => 'Username is not associated with an account'];
        }
        return ['email' => $user->email];
    }
}
