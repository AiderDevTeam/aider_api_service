<?php

namespace App\Http\Controllers;

use App\Actions\Booking\ConfirmBookingAction;
use App\Actions\Booking\ConfirmDropOffAction;
use App\Actions\Booking\ConfirmEarlyReturnAction;
use App\Actions\Booking\ConfirmPickupAction;
use App\Actions\Booking\CreateBookingAction;
use App\Actions\Booking\GetBookingsAction;
use App\Http\Requests\BookingConfirmationRequest;
use App\Http\Requests\BookingDropOffConfirmationRequest;
use App\Http\Requests\BookingPickupConfirmationRequest;
use App\Http\Requests\CreateBookingRequest;
use App\Models\Booking;
use App\Models\BookingProduct;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function createBooking(Request $request, CreateBookingRequest $bookingRequest, Product $product, CreateBookingAction $action): JsonResponse
    {
        return $action->handle($request, $bookingRequest, $product);
    }

    public function confirmBooking(Booking $booking, BookingConfirmationRequest $bookingRequest, ConfirmBookingAction $action): JsonResponse
    {
        return $action->handle($booking, $bookingRequest);
    }

    public function confirmPickup(Booking $booking, BookingPickupConfirmationRequest $request, ConfirmPickupAction $action): JsonResponse
    {
        return $action->handle($booking, $request);
    }

    public function confirmDropOff(Booking $booking, BookingDropOffConfirmationRequest $request, ConfirmDropOffAction $action): JsonResponse
    {
        return $action->handle($booking, $request);
    }

    public function bookings(Request $request, GetBookingsAction $action): JsonResponse
    {
        return $action->handle($request);
    }

    public function confirmEarlyReturn(BookingProduct $bookingProduct, ConfirmEarlyReturnAction $action): JsonResponse
    {
        return $action->handle($bookingProduct);
    }
}
