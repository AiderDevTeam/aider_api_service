<?php

namespace App\Http\Actions\Webhook;

use App\Models\Transaction;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DisbursementPaymentWebhookAction
{
    public function handle(Request $request): JsonResponse
    {
        logger('### DISBURSEMENT PAYMENT WEBHOOK ACTION ###');
        logger($request->all());
        try {
            if (!isset($request['transactionId']) || !isset($request['status'])) {
                logger('### Expected Request fields [transactionId, status] not set ###');
                return errorJsonResponse(errors: ['Expected Request fields not set'], statusCode: Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            if (!$transaction = Transaction::where('external_id', $request['transactionId'])->first()) {
                logger('### Transaction not found ###');
                return errorJsonResponse(errors: ['transaction not found for given reference'], statusCode: Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            if ($transaction->update([
                'status' => $request['status'],
            ])) {
                $transaction->payment->update(['disbursement_status' => $request['status']]);

                return successfulJsonResponse();
            }

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
