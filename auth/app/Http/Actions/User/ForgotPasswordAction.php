<?php

namespace App\Http\Actions\User;

use App\Http\Requests\ForgotPasswordRequest;
use App\Jobs\EmailNotificationJob;
use Exception;
use Illuminate\Http\JsonResponse;

class ForgotPasswordAction
{
    public function handle(ForgotPasswordRequest $request): JsonResponse
    {
        try {
            logger()->info('### FORGOT PASSWORD INITIATED ###');
            logger($request->validated());

            $otp = str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);
            $message = "Your One-Time Password (OTP) for verification is: $otp.\nThis will expire in 10 minutes.\n\nIf you did not request this, please ignore this email.\n\n- Aider";
            $subject = 'Forgot Password - OTP';

            EmailNotificationJob::dispatch(
                null,
                $request->validated('email'),
                $message,
                $subject
            )->onQueue('high');

            return successfulJsonResponse(['code' => $otp]);

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
