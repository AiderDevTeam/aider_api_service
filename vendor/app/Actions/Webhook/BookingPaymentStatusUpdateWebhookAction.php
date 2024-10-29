<?php

namespace App\Actions\Webhook;

use App\Custom\Status;
use App\Models\Booking;
use App\Models\Delivery;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BookingPaymentStatusUpdateWebhookAction
{
    public function handle(Request $request): JsonResponse
    {
        try {
            logger('### BOOKING PAYMENT WEBHOOK HANDLER ON VENDOR SERVICE ###');
            logger($request->all());

            if (!isset($request['paymentType']) || !isset($request['bookingExternalId'])) {
                logger('### Expected Request parameters not set ###');
                return errorJsonResponse(['Expected Request parameters not set'], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            return match ($request['paymentType']) {
                'collection' => $this->collectionStatusUpdate($request),
                'disbursement' => $this->disbursementStatusUpdate($request),
                default => null
            };

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }


    private function collectionStatusUpdate(Request $request): JsonResponse
    {
        try {
            if (!isset($request['collectionStatus'])) {
                logger('### Expected Request parameters not set ###');
                return errorJsonResponse(['Expected Request parameters not set'], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            if (Booking::findWithExternalId($request['bookingExternalId'])?->update(['collection_status' => $request['collectionStatus']]))
                return successfulJsonResponse([]);

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }

    private function disbursementStatusUpdate(Request $request): JsonResponse
    {
        try {
            if (!isset($request['disbursementStatus'])) {
                logger('### Expected Request parameters not set ###');
                return errorJsonResponse(['Expected Request parameters not set'], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            if (Booking::findWithExternalId($request['bookingExternalId'])?->update(['disbursement_status' => $request['disbursementStatus']]))
                return successfulJsonResponse([]);
        } catch (Exception $exception) {
            report($exception);
        }

        return errorJsonResponse();
    }
}
