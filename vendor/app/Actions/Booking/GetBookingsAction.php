<?php

namespace App\Actions\Booking;

use App\Http\Resources\BookingResource;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class GetBookingsAction
{
    public function handle(Request $request): JsonResponse
    {
        logger('### LOADING BOOKINGS ###');
        logger($request);
        try {
            if (!isset($request->type))
                return errorJsonResponse(errors: ['type is required'], statusCode: Response::HTTP_UNPROCESSABLE_ENTITY);

            if (!in_array($request->type, ['user', 'vendor']))
                return errorJsonResponse(errors: ['type must be either vendor or user'], statusCode: Response::HTTP_UNPROCESSABLE_ENTITY);

            $user = User::authUser($request->user);

            $bookings = match ($request->type) {
                'user' => $user->userPaidBookings(),
                default => $user->vendorPaidBookings()
            };

            return paginatedSuccessfulJsonResponse(BookingResource::collection(
                $bookings->with(
                    [
                        'bookedProduct.product.photos',
                        'bookedProduct.product.address',
                        'bookedProduct.bookingDates',
                        'bookedProduct.review',
                        'bookedProduct.renterReview.reviewer',
                        'bookedProduct.exchangeSchedule'
                    ]
                )->orderBy('created_at', 'DESC')->paginate($request->pageSize ?? 20)
            ));

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
