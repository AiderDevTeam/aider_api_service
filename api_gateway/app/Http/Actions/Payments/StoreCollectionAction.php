<?php

namespace App\Http\Actions\Payments;

use App\Custom\Status;
use App\Http\Requests\CollectionRequest;
use App\Models\Processor;
use App\Models\Transaction;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class StoreCollectionAction
{
    public function handle(CollectionRequest $request): JsonResponse
    {
        try {

            if (!$processor = Processor::collectionProcessor()) {
                logger()->info('### No Active Collection Processor Found ###');
                return errorJsonResponse(errors: ['No active collection processor found']);
            }

            $pendingTransactionDetails = [
                'transactionId' => $request->validated('transactionId'),
                'stan' => now()->format('YmdHisu'),
                'responseCode' => '111'
            ];

            $processor->transactions()->create([
                'external_id' => $request->validated('transactionId'),
                'amount' => toFloat($request->validated('amount')/100),
                'r_switch' => $request->validated('rSwitch'),
                'account_number' => $request->validated('accountNumber'),
                'description' => $request->validated('description'),
                'callback_url' => $request->validated('callbackUrl'),
                'status' => Status::PENDING,
                'response_code' => $pendingTransactionDetails['responseCode'],
                'response_message' => 'pending processing',
                'stan' => $pendingTransactionDetails['stan'],
                'type' => Transaction::MOMO_COLLECTION
            ]);

            logger()->info('### CLIENT REQUEST ###');
            logger($request->all());

            logger()->info('### SERVER RESPONSE ###');
            logger($pendingTransactionDetails);

            return successfulJsonResponse(
                $pendingTransactionDetails,
                'request accepted and pending processing',
                Response::HTTP_ACCEPTED
            );

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
