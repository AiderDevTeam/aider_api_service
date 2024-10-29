<?php

namespace App\Actions\Booking;

use App\Http\Requests\BookingPickupConfirmationRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ConfirmPickupAction
{
    public function handle(Booking $booking, BookingPickupConfirmationRequest $request): JsonResponse
    {
        logger('### BOOKING PICKUP CONFIRMATION ###');
        logger($payload = $request->validated());
        try {
            $confirmationData = match ($payload['type']) {
                'user' => ['user_pickup_status' => $payload['status']],
                'vendor' => ['vendor_pickup_status' => $payload['status']],
                default => null
            };

            if (is_null($confirmationData)) {
                return errorJsonResponse(
                    errors: ['pick up confirmation not successful. Try again after sometime'],
                    statusCode: Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }

            if ($booking->update($confirmationData)) {
                return successfulJsonResponse(
                    new BookingResource($booking->refresh())
                );
            }

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
