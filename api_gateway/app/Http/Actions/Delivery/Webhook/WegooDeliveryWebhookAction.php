<?php

namespace App\Http\Actions\Delivery\Webhook;

use App\Custom\Status;
use App\Models\Delivery;
use App\Models\WegooDelivery;
use Exception;
use Illuminate\Http\Request;

class WegooDeliveryWebhookAction
{
    public function handle(Request $request)
    {
        try {
            logger()->info('### WEGOO DELIVERY WEBHOOK EVENT ###');
            logger($response = $request->all());
            if (isset($response['tracking_number']) && isset($response['status'])) {

                if ($delivery = Delivery::findWithTrackingNumber($response['tracking_number'])) {

                    logger('### UPDATING WEGOO DELIVERY LOG ###');
                    $wegooDeliveryLog = $delivery->deliveryProcessorLog;

                    $wegooDeliveryLog->update([
                        'response_payload' =>
                            json_encode([
                                ...json_decode($wegooDeliveryLog->response_payload, true),
                                $response
                            ])
                    ]);

//                    $delivery->update([
//                        'status' => Status::DELIVERY_STATUS[$response['status']] ?? Status::DELIVERY_STATUS['PENDING']
//                    ]);

                    return successfulJsonResponse();
                }
                logger('### COULD NOT FIND DELIVERY BY TRACKING NUMBER ###');
                return errorJsonResponse(message: 'Could not find delivery by tracking number');
            }
            logger('### ARRAY KEYS NOT SET ###');
            return errorJsonResponse(message: 'array keys not found');
        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
