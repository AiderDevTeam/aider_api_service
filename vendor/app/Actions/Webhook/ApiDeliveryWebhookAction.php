<?php

namespace App\Actions\Webhook;

use App\Custom\Status;
use App\Models\Delivery;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiDeliveryWebhookAction
{
    public function handle(Request $request): JsonResponse
    {
        try {
            logger('### API GATEWAY DELIVERY WEBHOOK HANDLER ON VENDOR ###');
            logger($request->all());

            if (isset($request['externalId']) && isset($request['status']) && isset($request['trackingNumber'])) {
                if ($delivery = Delivery::where('external_id', '=', $request['externalId'])->first()) {

                    if ($request['status'] === Status::PENDING)
                        return successfulJsonResponse([]);

                    $delivery->order->update([
                        'status' => $request['status']
                    ]);

                    $delivery->update([
                        'status' => $request['status'],
                        'tracking_number' => $request['trackingNumber']
                    ]);
                    return successfulJsonResponse([]);
                }
                logger('### Delivery not found ###');
                return errorJsonResponse(['Delivery not found']);
            } else {
                logger('### Expected Request parameters not set ###');
                return errorJsonResponse(['Expected Request parameters not set']);
            }
        } catch (Exception $exception) {
            report($exception);
        }
        logger('### Something Went Wrong ###');
        return errorJsonResponse();
    }
}
