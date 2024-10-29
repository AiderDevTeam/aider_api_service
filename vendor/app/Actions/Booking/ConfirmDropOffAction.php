<?php

namespace App\Actions\Booking;

use App\Http\Requests\BookingDropOffConfirmationRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ConfirmDropOffAction
{
    public function handle(Booking $booking, BookingDropOffConfirmationRequest $request): JsonResponse
    {
        logger('### BOOKING DROP OFF CONFIRMATION ###');
        logger($payload = $request->validated());
        try {
            $confirmationData = match ($payload['type']) {
                'user' => ['user_drop_off_status' => $payload['status']],
                'vendor' => ['vendor_drop_off_status' => $payload['status']],
                default => null
            };

            if (is_null($confirmationData)) {
                return errorJsonResponse(
                    errors: ['Drop Off confirmation not successful. Try again after sometime'],
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
