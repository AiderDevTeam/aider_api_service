<?php

namespace App\Http\Actions\Payments;

use App\Custom\Status;
use App\Http\Requests\DisbursementRequest;
use App\Models\Processor;
use App\Models\Transaction;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class StoreDisbursementAction
{
    public function handle(DisbursementRequest $request): JsonResponse
    {
        try {
            if (!$processor = Processor::disbursementProcessor()) {
                logger()->info('### No Active Disbursement Processor Found ###');
                return errorJsonResponse(errors: ['No active disbursement processor found']);
            }
            $requestPayload = $request->validated();

            $pendingTransactionDetails = [
                'transactionId' => $requestPayload['transactionId'],
                'stan' => now()->format('YmdHisu'),
                'responseCode' => '111'
            ];

            $processor->transactions()->create([
                'external_id' => $requestPayload['transactionId'],
                'amount' => $requestPayload['amount'],
                'r_switch' => $requestPayload['rSwitch'],
                'account_number' => $requestPayload['accountNumber'],
                'recipient_code' => $requestPayload['recipientCode'],
                'description' => $requestPayload['description'],
                'callback_url' => $requestPayload['callbackUrl'],
                'status' => Status::PENDING,
                'response_code' => $pendingTransactionDetails['responseCode'],
                'response_message' => 'pending processing',
                'stan' => $pendingTransactionDetails['stan'],
                'type' => $requestPayload['type']
            ]);

            logger()->info('### CLIENT REQUEST ###');
            logger($request->all());

            logger()->info('### SERVER RESPONSE ###');
            logger($pendingTransactionDetails);

            return successfulJsonResponse(
                $pendingTransactionDetails,
                $requestPayload['type'] . ' request accepted and pending processing',
                Response::HTTP_ACCEPTED
            );

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
