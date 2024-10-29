<?php

namespace App\Http\Actions;

use App\Http\Requests\EmailRequest;
use App\Http\Services\EmailService;
use Exception;
use Illuminate\Http\JsonResponse;

class EmailAction
{
    public function handle(EmailRequest $request): JsonResponse
    {
        logger()->info('### SENDING EMAIL ###');
        logger($data = $request->validated());

        try {
            EmailService::send(
                $data['recipientEmail'],
                $data['message'],
                $data['subject'],
            );

            return successfulJsonResponse();

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
