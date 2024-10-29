<?php

namespace App\Actions\Booking;

use App\Http\Resources\BookingResource;
use App\Models\BookingProduct;
use Exception;
use Illuminate\Http\JsonResponse;

class ConfirmEarlyReturnAction
{
    public function handle(BookingProduct $bookingProduct): JsonResponse
    {
        logger('### CONFIRMING EARLY RETURN ###', [$bookingProduct->external_id]);
        try {

            $bookingProduct->update(['returned_early' => true]);

            manuallySyncModels([$bookingProduct->booking->message]);

            return successfulJsonResponse(new BookingResource($bookingProduct->refresh()->booking->load(['bookedProduct.product.photos', 'bookedProduct.product.address', 'bookedProduct.bookingDates'])));

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
