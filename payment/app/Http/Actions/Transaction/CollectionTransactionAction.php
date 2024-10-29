<?php

namespace App\Http\Actions\Transaction;

use App\Enum\Status;
use App\Http\Requests\TransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\BookingPayment;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CollectionTransactionAction
{
    public function handle(Request $request, TransactionRequest $transactionRequest): JsonResponse
    {
        logger('### COLLECTION TRANSACTION INITIALIZED ###');
        logger($payload = $transactionRequest->validated());
        try {
            DB::beginTransaction();
            $user = User::authUser($request->user);

            $payment = $this->bookingPayment($payload)
                ->payment()->create([
                    'user_id' => $user->id,
                    ...$this->getPaymentData($payload)
                ]);

            $transaction = $payment->transactions()->create([
                'user_id' => $user->id,
                'amount' => $payload['amount'],
                'account_number' => $payload['accountNumber'] ?? null,
                'stan' => generateStan(),
                'sort_code' => $payload['sortCode'] ?? null,
                'type' => $payload['type'],
            ]);
            DB::commit();

            return successfulJsonResponse(new TransactionResource($transaction));
        } catch (Exception $exception) {
            DB::rollBack();
            report($exception);
        }
        return errorJsonResponse();
    }

    private function getPaymentData(array $data): ?array
    {
        return [
            'collection_amount' => $data['amount'],
            'collection_status' => Status::PENDING,
            'collection_account_number' => $data['accountNumber'] ?? null,
            'collection_account_sort_code' => $data['sortCode'] ?? null
        ];
    }

    private function bookingPayment(array $data): Model|Builder
    {
        return BookingPayment::query()->create([
            'booking_external_id' => $data['paymentTypeExternalId'],
            'callback_url' => env('VENDOR_BASE_URL') . '/webhooks/booking-payment-status-response'
        ]);
    }
}
