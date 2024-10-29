<?php

namespace App\Actions\Report;

use App\Http\Requests\ReportRequest;
use App\Models\Booking;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class ReportAction
{
    public function handle(Request $request, User $reportedUser, ReportRequest $reportRequest): JsonResponse
    {
        try {
            logger('### REPORTING A SHOP OR PRODUCT ###');
            logger($validatedRequest = $reportRequest->validated());

            $authUser = User::authUser($request->user);

            $booking = Booking::find($validatedRequest['bookingId']);

            if ($authUser->hasReportedBooking($booking)) {
                return errorJsonResponse(errors: ['You have already made a report on this booking.'], statusCode: Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $reportedUser->reports()->create([
                'reporter_id' => $authUser->id,
                'booking_id' => $validatedRequest['bookingId'],
                'reason' => $validatedRequest['reason']
            ]);

            manuallySyncModels([$booking->message]);

            return successfulJsonResponse(data: [], message: 'Report has been made');

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
