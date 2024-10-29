<?php

namespace App\Actions\Webhook;

use App\Custom\Status;
use App\Models\Delivery;
use App\Models\Order;
use Exception;
use Illuminate\Http\Request;

class DisbursementCallbackWebhookAction
{
    public function handle(Request $request)
    {
        try {
            logger('### DISBURSEMENT CALLBACK WEBHOOK HANDLE ON VENDOR ###');
            logger($request->all());

            if (isset($request['deliveryExternalId']) && isset($request['disbursementStatus'])) {
                if ($delivery = Delivery::where('external_id', '=', $request['deliveryExternalId'])->first()) {

                    $order = $delivery->order;

                    if ($order->isReversing()) {
                        $this->orderReversalDisbursement($order, $request);
                        return successfulJsonResponse(data: [], statusCode: 204);
                    }

                    $this->vendorPayoutDisbursement($order, $request);
                    return successfulJsonResponse(data: [], statusCode: 204);
                }
                logger('### Delivery not found ###');
                return errorJsonResponse(['Delivery not found']);
            }
        } catch (Exception $exception) {
            report($exception);
        }
        logger('### Something Went Wrong ###');
        return errorJsonResponse();
    }

    private function orderReversalDisbursement(Order $order, Request $request): void
    {
        logger()->info("### UPDATING ORDER ON {$request['disbursementStatus']} REVERSAL ###");

        $order->update([
            'reversal_status' => $request['disbursementStatus']
        ]);
    }

    private function vendorPayoutDisbursement(Order $order, Request $request): void
    {
        logger()->info("### UPDATING ORDER ON {$request['disbursementStatus']} VENDOR PAYOUT ###");

        $order->update([
            'disbursement_status' => $request['disbursementStatus']
        ]);
    }

}
