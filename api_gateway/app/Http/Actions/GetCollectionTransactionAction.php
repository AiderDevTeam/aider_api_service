<?php

namespace App\Http\Actions;

use App\Models\HubtelPayment;
use App\Models\Transaction;
use Exception;
use Illuminate\Http\JsonResponse;

class GetCollectionTransactionAction
{
    public function handle(string $transactionExternalId): JsonResponse
    {
        try {
            logger('### GETTING COLLECTION TRANSACTION WITH ID ###', [$transactionExternalId]);
            if ($transaction = Transaction::where('external_id', '=', $transactionExternalId)
                ->whereIn('type', [Transaction::MOMO_COLLECTION])
                ->first()) {

                $transactionLog = HubtelPayment::where('transaction_id', $transaction->id)->first();

                return successfulJsonResponse([
                    'transactionId' => $transaction->external_id,
                    'amount' => $transaction->amount,
                    'stan' => $transaction->stan,
                    'rSwitch' => $transaction->r_switch,
                    'accountNumber' => $transaction->account_number,
                    'status' => $transaction->status,
                    'type' => $transaction->type,
                    'description' => $transaction->description,
                    'responseCode' => $transaction->response_code,
                    'code' => $transaction->response_code,
                    'responseMessage' => $transaction->response_message,
                    'callbackUrl' => $transaction->callback_url,
                    'responsePayload' => $transactionLog->response_payload
                ]);
            }
            return errorJsonResponse(message: 'transaction not found', statusCode: 404);
        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
