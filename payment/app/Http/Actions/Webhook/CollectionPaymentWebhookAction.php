<?php

namespace App\Http\Actions\Webhook;

use App\Models\Transaction;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CollectionPaymentWebhookAction
{
    public function handle(Request $request): JsonResponse
    {
        logger()->info('### COLLECTION PAYMENT WEBHOOK ACTION ###');
        logger($request->all());
        try {
            $requestPayload = $request['data'];

            if (!isset($requestPayload['reference']) || !isset($requestPayload['status'])) {
                logger('### Expected Request fields [reference, status] not set ###');
                return errorJsonResponse(errors: ['Expected Request fields not set'], statusCode: Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            if (!$transaction = Transaction::where('stan', $requestPayload['reference'])->first()) {
                logger('### Transaction not found ###');
                return errorJsonResponse(errors: ['transaction not found for given reference'], statusCode: Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            if ($transaction->update([
                'status' => $requestPayload['status'],
                'account_number' => $requestPayload['account_number'] ?? null,
                'sort_code' => $requestPayload['subaccount']['settlement_bank'] ?? null
            ])) {
                $transaction->payment->update(['collection_status' => $requestPayload['status']]);

                return successfulJsonResponse();
            }

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
