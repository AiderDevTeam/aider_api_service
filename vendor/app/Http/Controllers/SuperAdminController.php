<?php

namespace App\Http\Controllers;

use App\Actions\Booking\GetBookingAction;
use App\Actions\Report\GetReportsAction;
use App\Actions\Report\ResolveReportAction;
use App\Actions\SuperAdmin\GetProductsByStatusAction;
use App\Http\Resources\BookingResource;
use App\Http\Resources\UserResource;
use App\Models\Booking;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    public function productsByStatus(Request $request, GetProductsByStatusAction $action): JsonResponse
    {
        return $action->handle($request);
    }

    public function getBookings(Request $request): JsonResponse
    {
        return paginatedSuccessfulJsonResponse(BookingResource::collection(
            Booking::query()->with(
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
    }

    public function getReports(Request $request, GetReportsAction $action): JsonResponse
    {
        return $action->handle($request);
    }

    public function resolveReport(Request $request, Report $report, ResolveReportAction $action): JsonResponse
    {
        return $action->handle($request, $report);
    }

    public function getBooking(Booking $booking): JsonResponse
    {
        $booking->load(
            [
                'bookedProduct.product.photos',
                'bookedProduct.product.address',
                'bookedProduct.bookingDates',
                'bookedProduct.review',
                'bookedProduct.renterReview.reviewer',
                'bookedProduct.exchangeSchedule',
                'user',
                'vendor'
            ]
        );

        return successfulJsonResponse(new BookingResource($booking));
    }

    public function getUser(User $user): JsonResponse
    {
        return successfulJsonResponse(new UserResource($user));
    }
}
