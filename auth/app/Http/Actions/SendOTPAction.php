<?php

namespace App\Http\Actions;

use App\Http\Requests\OTPRequest;
use App\Http\Services\API\EmailService;
use App\Jobs\EmailNotificationJob;
use Exception;
use Illuminate\Http\JsonResponse;

class SendOTPAction
{
    public function handle(OTPRequest $request): JsonResponse
    {
        logger('### SENDING OTP ON SIGNUP ###');
        logger($request->validated());

        try {
            $otp = generateOTP();
            $message = "Your One-Time Password (OTP) for verification is: $otp.\n\n This will expire in 10 minutes.\nIf you did not request this, please ignore this email.\n\n- Aider";
            $subject = 'Signup - OTP';

            EmailService::send($request->validated('email'), $message, $subject);

            return successfulJsonResponse(['code' => $otp]);

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }

}
