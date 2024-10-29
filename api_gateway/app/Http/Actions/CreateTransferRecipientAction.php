<?php

namespace App\Http\Actions;

use App\Http\Requests\CreatePaystackTransferRecipientRequest;
use App\Http\Services\PaystackService;
use Exception;
use Illuminate\Http\JsonResponse;

class CreateTransferRecipientAction
{
    public function handle(CreatePaystackTransferRecipientRequest $request): JsonResponse
    {
        logger('### CREATING TRANSFER RECIPIENT ###');
        logger($payload = $request->validated());
        try {

            $response = PaystackService::createTransferRecipient($payload);

            if (isset($response['data']['recipient_code'])) {
                return successfulJsonResponse(['recipientCode' => $response['data']['recipient_code']]);
            }

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
