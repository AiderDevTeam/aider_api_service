<?php

namespace App\Http\Actions;

use App\Models\PaystackPayment;
use App\Models\Transaction;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PaystackWebhookAction
{
    public function handle(Request $request): JsonResponse
    {
        try {
            logger('### PAYSTACK WEBHOOK ACTION PROCESSING ###');
            logger($response = $request->all());

            if (isset($response['data']['status']) && isset($response['data']['reference']) && isset($response['event'])) {

                if ($transaction = Transaction::getByStan($response['data']['reference'])) {
                    $this->handleDisbursementWebhookAction($response, $transaction);
                } else {
                    $this->handleCollectionWebhookAction($response);
                }

            } else {
                logger()->error('### Expected data keys not set or found ###');
            }

        } catch (Exception $exception) {
            logger()->error($exception->getMessage());
            report($exception);
        }
        return successfulJsonResponse(); //return status code 200 to paystack to prevent further webhook hits
    }

    private function handleDisbursementWebhookAction($response, $transaction): void
    {
        try {
            logger('### PROCESSING DISBURSEMENT WEBHOOK RESPONSE ###');
            logger($response['event']);

            match ($response['event']) {
                'transfer.success' => $transaction->success(),
                'transfer.pending' => $transaction->pending(),
                default => $transaction->fail()
            };

            if ($paymentLog = PaystackPayment::query()->where('transaction_id', $transaction->id)?->first()) {
                $responsePayload = is_null($paymentLog->response_payload) ? [] : $paymentLog->response_payload;

                $paymentLog->update([
                    'response_payload' => [
                        $responsePayload,
                        $response
                    ]
                ]);
            }

        } catch (Exception $exception) {
            report($exception);
        }
    }

    private function handleCollectionWebhookAction($response): void
    {
        try {
            logger()->info('### Disbursement Transaction not found for provided paystack reference ###');
            logger()->info('### Dispatching collection transaction status check to payment service ###');

            logger($url = env('PAYMENT_BASE_URL') . '/api/sys/collection/status-check/' . $response['data']['reference']);

            $paymentServiceResponse = Http::get($url);

            logger()->info('### RESPONSE FROM PAYMENT SERVICE ###');
            logger($paymentServiceResponse);

        } catch (Exception $exception) {
            report($exception);
        }
    }
}
