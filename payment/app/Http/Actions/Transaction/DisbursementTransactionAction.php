<?php

namespace App\Http\Actions\Transaction;

use App\Enum\Status;
use App\Http\Requests\TransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\BookingPayment;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class DisbursementTransactionAction
{
    public function handle(TransactionRequest $transactionRequest): JsonResponse
    {
        logger('### DISBURSEMENT TRANSACTION INITIALIZED ###');
        logger($payload = $transactionRequest->validated());
        try {
            if (!$bookingPayment = BookingPayment::query()->where('booking_external_id', $payload['paymentTypeExternalId'])->first()) {
                return errorJsonResponse(message: 'booking payment not found for given external id', statusCode: Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $user = User::query()->firstOrCreate(['external_id' => $payload['userExternalId']], ['details' => []]);

            if (!$wallet = $user->defaultWallet()) {
                return errorJsonResponse(message: 'no default wallet found for user', statusCode: Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $bookingPayment->payment->update([
                'disbursement_amount' => $payload['amount'],
                'disbursement_status' => Status::PENDING->value,
                'disbursement_account_number' => $wallet->account_number,
                'disbursement_account_sort_code' => $wallet->sort_code,
            ]);

            $transaction = $bookingPayment->payment->transactions()->create([
                'user_id' => $user->id,
                'amount' => $payload['amount'],
                'account_number' => $wallet->account_number,
                'stan' => generateStan(),
                'sort_code' => $wallet->sort_code,
                'type' => $payload['type'],
                'bank_code' => $wallet->bank_code,
                'recipient_code' => $wallet->recipient_code,
                'callback_url' => env('PAYMENT_BASE_URL').'/webhooks/disbursement-payment-webhook'
            ]);

            return successfulJsonResponse(new TransactionResource($transaction));

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
