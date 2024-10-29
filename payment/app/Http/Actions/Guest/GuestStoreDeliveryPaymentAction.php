<?php

namespace App\Http\Actions\Guest;

use App\Http\Requests\GuestStoreDeliveryPaymentRequest;
use App\Http\Resources\TransactionResource;
use App\Jobs\CollectionJob;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class GuestStoreDeliveryPaymentAction
{
    public function handle(GuestStoreDeliveryPaymentRequest $deliveryPaymentRequest): JsonResponse
    {

        logger('Guest Delivery Payment Request ::::::::',[$deliveryPaymentRequest->validated()]);
        try {
            if (!$deliveryPaymentRequest->validated()) {
                return errorJsonResponse();
            }

            $deliveryExternalId = $deliveryPaymentRequest->deliveryExternalId;

            $user = $this->createGuestUser($deliveryPaymentRequest['guestExternalId']);

            $guestUser = $user->wallets()->updateOrCreate(
                ['account_number' => $deliveryPaymentRequest['collectionWallet']['accountNumber']],
                [
                'type' => 'momo',
                'sort_code' => $deliveryPaymentRequest['collectionWallet']['sortCode'],
                    'account_number' => $deliveryPaymentRequest['collectionWallet']['accountNumber'],
                'account_name' => $deliveryPaymentRequest['collectionWallet']['accountName']
            ]);

            getOrCreateUser($deliveryPaymentRequest['vendorExternalId']);

            $collectionWallet = $deliveryPaymentRequest['collectionWallet'];

            $deliveryPaymentData = [
                'description' => $deliveryPaymentRequest->description ?? '',
                'amount' => $deliveryPaymentRequest->amount,
                'delivery_external_id' => $deliveryExternalId,
            ];

            DB::beginTransaction();

            try {
                $deliveryPayment = $user->deliveryPayments()->create($deliveryPaymentData);

                $paymentData = [
                    'collection_account_name' => $collectionWallet['accountName'],
                    'collection_account_number' => $collectionWallet['accountNumber'],
                    'collection_sort_code' => $collectionWallet['sortCode'],
                    'destination_account_name' => $collectionWallet['accountName'],
                    'destination_account_number' => $collectionWallet['accountNumber'],
                    'destination_sort_code' => $collectionWallet['sortCode'],
                    'user_id' => $user->id,
                    'source_external_id' => $guestUser->external_id,
                    'amount' => $deliveryPaymentRequest->amount,
                    'value' => $deliveryPaymentRequest->amount,
                    'type' => 'delivery'
                ];

                $payment = $deliveryPayment->payment()->create($paymentData);

                $transactionData = [
                    'user_id' => $user->id,
                    'amount' => $deliveryPaymentRequest->amount,
                    'account_number' => $collectionWallet['accountNumber'],
                    'r_switch' => $collectionWallet['sortCode'],
                    'callback_url' => $deliveryPaymentRequest->callbackUrl
                ];

                $transaction = $payment->transaction()->create($transactionData);


                DB::commit();

                $transaction->refresh();

                CollectionJob::dispatchSync($payment);

                return successfulJsonResponse(new TransactionResource($transaction));

            } catch (Exception $exception) {
                DB::rollBack();
                report($exception);
                return errorJsonResponse();
            }

        } catch (Exception $exception) {
            report($exception);
        }

        return errorJsonResponse();
    }

    private function createGuestUser($externalId): Model
    {
        logger('inside guest user');
        return getOrCreateUser($externalId);
    }

}
