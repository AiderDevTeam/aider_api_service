<?php

namespace App\Listeners;

use App\Custom\BookingStatus;
use App\Events\SuccessfulBookingEvent;
use App\Jobs\PayoutJob;
use App\Models\Booking;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SuccessfulBookingListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(SuccessfulBookingEvent $event): void
    {
        $booking = $event->booking;
        logger()->info('### ATTEMPTING DISBURSEMENT ###');
        try {
            if ($booking->isCompleted()) {

                $booking->updateQuietly([
                    'status' => BookingStatus::SUCCESS,
                    'disbursement_status' => BookingStatus::PENDING
                ]);

                PayoutJob::dispatch([
                    'userExternalId' => $booking->vendor->external_id,
                    'paymentType' => 'booking',
                    'paymentTypeExternalId' => $booking->external_id,
                    'amount' => $booking->disbursement_amount,
                    'type' => 'disbursement'
                ])->onQueue('high');

                $this->increaseProductQuantityOnSuccessfulBooking($booking);

                return;
            }
            logger('### BOOKING NOT COMPLETED. DISBURSEMENT ATTEMPT ABORTED ###');

        } catch (Exception $exception) {
            report($exception);
        }
    }

    private function increaseProductQuantityOnSuccessfulBooking(Booking $booking): void
    {
        logger('### INCREASING PRODUCT QUANTITY AFTER COMPLETED BOOKING ###');

        $bookedProduct = $booking->bookedProduct;
        $bookedProduct->product()->increment('quantity', $bookedProduct->product_quantity);
    }
}
