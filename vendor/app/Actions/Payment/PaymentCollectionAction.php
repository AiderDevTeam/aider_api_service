<?php

namespace App\Actions\Payment;

use App\Custom\Status;
use App\Http\Services\Payment\BookingPaymentService;
use App\Models\Booking;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PaymentCollectionAction
{
    public function handle(Request $request, Booking $booking): JsonResponse
    {
        try {
            logger("### BOOKING COLLECTION PAYMENT INITIALIZED FOR BOOKING [$booking->external_id] ###");

            $response = (new BookingPaymentService($request, [
                'paymentTypeExternalId' => $booking->external_id,
                'amount' => $booking->collection_amount,
            ]))->initializeCollection();

            if (!is_null($response)) {
                $booking->update(['collection_status' => Status::PENDING]);

                return successfulJsonResponse([
                    'stan' => $response['data']['stan']
                ], message: 'collection started');
            }

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse(errors: ['Payment cannot be completed at this time. Try again after sometime'], statusCode: Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
