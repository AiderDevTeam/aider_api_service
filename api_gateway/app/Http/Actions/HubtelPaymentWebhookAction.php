<?php

namespace App\Http\Actions;

use App\Models\HubtelPayment;
use App\Models\Transaction;
use Exception;
use Illuminate\Http\Request;

class HubtelPaymentWebhookAction
{
    public function handle(Request $request)
    {
        try {

            logger()->info('### HUBTEL PAYMENT WEBHOOK EVENT ###');
            logger($response = $request->all());

            if (isset($response['Data']['ClientReference']) && $transaction = Transaction::getByStan($response['Data']['ClientReference'])) {
                if ($paymentLog = HubtelPayment::query()->where('transaction_id', '=', $transaction->id)->first()) {

                    $responsePayload = is_null($paymentLog->response_payload) ? [] : [...$paymentLog->response_payload];
                    $paymentLog->update([
                        'response_payload' => [
                            $responsePayload,
                            $response
                        ]
                    ]);
                }

                if (isset($response['ResponseCode'])) {
                    match ($response['ResponseCode']) {
                        '0000' => $transaction->success(),
                        '0001' => $transaction->pending(),
                        default => $transaction->fail()
                    };
                }
            }

//            if (isset($response['Data']['RecurringInvoiceId'])) event(new UpdateHubtelPaymentLogFromCallbackUrlEvent($response));

            return response()->json();

        } catch (Exception $exception) {
            report($exception);
        }
        return null;
    }
}
