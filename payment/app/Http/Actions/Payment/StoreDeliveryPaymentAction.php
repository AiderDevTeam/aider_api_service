<?php

namespace App\Http\Actions\Payment;

use App\Http\Requests\Payment\StoreDeliveryPaymentRequest;

use App\Http\Resources\TransactionResource;
use App\Jobs\CollectionJob;

use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StoreDeliveryPaymentAction
{

    public function handle(?Request $authRequest, StoreDeliveryPaymentRequest $deliveryPaymentRequest): JsonResponse
    {

        logger('Delivery Payment Request ::::::::',[$deliveryPaymentRequest->validated()]);
        try {
            if (!$deliveryPaymentRequest->validated()) {
                return errorJsonResponse();
            }

            $deliveryExternalId = $deliveryPaymentRequest->deliveryExternalId;

            if ($authRequest) {
                $user = getOrCreateUser($authRequest->user['externalId']);
            } else $user = $this->createGuestUser($deliveryPaymentRequest['guestExternalId']);

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
                    'source_external_id' => $collectionWallet['externalId'],
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

    private function createGuestUser($externalId): User
    {
        return getOrCreateUser($externalId);
    }

}
