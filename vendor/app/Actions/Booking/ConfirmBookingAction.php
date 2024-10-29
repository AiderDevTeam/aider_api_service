<?php

namespace App\Actions\Booking;

use App\Http\Requests\BookingConfirmationRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use Exception;
use Illuminate\Http\Client\Request;
use Illuminate\Http\JsonResponse;

class ConfirmBookingAction
{
    public function handle(Booking $booking, BookingConfirmationRequest $request): JsonResponse
    {
        logger("### CONFIRMING BOOKING ###");
        logger($request->validated());
        try {
            $booking->update(['booking_acceptance_status' => $request->validated('status')]);

            if ($booking->wasNotAccepted()) {
                $booking->fail();
                $booking->message?->conversation?->end();
            }

            return successfulJsonResponse(new BookingResource($booking->refresh()->load(['bookedProduct.product.photos', 'bookedProduct.product.address', 'bookedProduct.bookingDates'])));
        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
