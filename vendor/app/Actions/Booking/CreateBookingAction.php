<?php

namespace App\Actions\Booking;

use App\Http\Requests\CreateBookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\BookingProduct;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class CreateBookingAction
{
    public function handle(Request $request, CreateBookingRequest $bookingRequest, Product $product): JsonResponse
    {
        logger('### PRODUCT BOOKING INITIALIZED ###');
        logger($bookingData = $bookingRequest->validated());
        try {
            $user = User::authUser($request->user);

            if ($user->ownsProduct($product)) {
                return errorJsonResponse(message: 'Oops! You can\'t rent your own item.', statusCode: Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $bookingDates = $this->getBookingDates($bookingData['startDate'], $bookingData['endDate']);

            if (!$productPrice = $this->getPrice($product, $bookingDates['duration'])?->price) {
                return errorJsonResponse(message: 'No price found for selected number of booking days', statusCode: Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            DB::beginTransaction();

            $bookingPrice = ($productPrice * $bookingData['quantity']);

            $booking = $user->userBookings()->create([
                'vendor_id' => $product->vendor->id,
                // 'collection_amount' => $bookingPrice * (1 + (Setting::serviceFee() ?? 0.01)),
                'collection_amount' => $bookingPrice * (1 + ((Setting::serviceFee() ?? 0.01) * $bookingDates['duration'])),
                'disbursement_amount' => $bookingPrice,
                'booking_number' => generateBookingNumber(),
            ]);

            $bookedProduct = $booking->bookedProduct()->create([
                'external_id' => uniqid('BP'),
                'product_id' => $product->id,
                'product_amount' => $productPrice,
                'product_quantity' => $bookingData['quantity'],
                'product_value' => $product->value,
                'start_date' => $bookingData['startDate'],
                'end_date' => $bookingData['endDate'],
                'booking_duration' => $bookingDates['duration']
            ]);

            $bookedProduct->bookingDates()->createMany($bookingDates['dates']);

            $product->reduceQuantityOnBooking($bookingData['quantity']);

            $bookedProduct->exchangeSchedule()->create(arrayKeyToSnakeCase($bookingData['exchangeSchedule']));

            DB::commit();

            manuallySyncModels([$booking->refresh()->message]);

            return successfulJsonResponse(
                new  BookingResource($booking->refresh())
            );

        } catch (Exception $exception) {
            DB::rollBack();
            report($exception);
        }
        return errorJsonResponse();
    }

    private function getBookingDates(string $startDate, string $endDate): array
    {
        $bookingDates = collect(
            CarbonPeriod::create(
                Carbon::createFromFormat('Y-m-d', $startDate),
                CarbonInterval::create('P1D'), //Create a DateInterval representing a period of 1 day
                Carbon::createFromFormat('Y-m-d', $endDate)
            )
        )->map(fn($bookingDate) => [
            'booking_date' => $bookingDate->format('Y-m-d')
        ])->all();

        return [
            'dates' => $bookingDates,
            'duration' => count($bookingDates)
        ];
    }

    private function getPrice(Product $product, int $bookingDuration): Model|HasMany|null
    {
        if ($finiteEndDay = $product->prices()->where('start_day', '<=', $bookingDuration)
            ->where('end_day', '>=', $bookingDuration)?->first()) {
            return $finiteEndDay;
        }

        return $product->prices()->whereNull('end_day')
            ->where('start_day', '<=', $bookingDuration)?->first();

    }
}
