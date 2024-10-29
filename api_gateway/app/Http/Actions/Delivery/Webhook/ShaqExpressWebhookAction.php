<?php

namespace App\Http\Actions\Delivery\Webhook;

use App\Custom\Status;
use App\Models\Delivery;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShaqExpressWebhookAction
{
    public function handle(Request $request): JsonResponse
    {
        try {
            logger()->info('### SHAQ EXPRESS WEBHOOK EVENT ###');
            logger($response = $request->all());

            if (!isset($response['booking_id']) || !isset($response['status'])) {
                logger('### ARRAY KEYS NOT SET ###');
                return errorJsonResponse(errors: ['Expected array keys not set']);
            }

            if ($delivery = Delivery::findWithTrackingNumber($response['booking_id'])) {

                logger('### UPDATING SHAQ EXPRESS DELIVERY LOG ###');
                $shaqExpressDeliveryLog = $delivery->deliveryProcessorLog;

                $shaqExpressDeliveryLog->update([
                    'response_payload' =>
                        json_encode([
                            ...json_decode($shaqExpressDeliveryLog->response_payload, true),
                            $response
                        ])
                ]);

                $delivery->update([
                    'status' => self::handleStatusChanges($response['status'])
                ]);

                return successfulJsonResponse();
            }
            logger('### COULD NOT FIND DELIVERY BY BOOKING ID NUMBER ###');
            return errorJsonResponse(message: 'Could not find delivery by booking id');

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }

    private static function handleStatusChanges(string $status): string
    {
        return match ($status) {
            'assigned', 'reassigned' => Status::DELIVERY_STATUS['ASSIGNED'],
            'accepted' => Status::DELIVERY_STATUS['DISPATCHED'],
            'started' => Status::DELIVERY_STATUS['TO_RECIPIENT'],
            'completed' => Status::DELIVERY_STATUS['PAID'],
            default => Status::DELIVERY_STATUS['PENDING']
        };
    }
}
