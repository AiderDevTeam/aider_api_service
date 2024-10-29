<?php

namespace App\Http\Actions\User;

use App\Http\Requests\PasswordResetRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;

class PasswordResetAction
{
    public function handle(PasswordResetRequest $request): JsonResponse
    {
        try {
            logger()->info('### PASSWORD RESET INITIATED ###');
            logger($request->except('password'));
            $requestPayload = $request->validated();

            if (User::findWithEmail($requestPayload['email'])?->update(['password' => $requestPayload['password']])) {
                logger()->info('### PASSWORD RESET SUCCESSFUL ###');
                return successfulJsonResponse(message: 'Password reset successful');
            }
        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
