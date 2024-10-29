<?php

namespace App\Listeners;

use App\Events\PendingPickupBookingsRecordingEvent;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class PendingPickupBookingsRecordingListener
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
    public function handle(PendingPickupBookingsRecordingEvent $event): void
    {
        logger('### PENDING PICKUP BOOKINGS COUNT RECORDING EVENT TRIGGERED ##');

        $booking = $event->booking;
        try {
            if ($booking->collectionSuccessful() && $booking->isAccepted()) {

                $booking->user->recordRenterBookingsPendingPickupCount();
                $booking->vendor->recordVendorBookingsPendingPickupCount();

                manuallySyncModels([$booking->user, $booking->vendor]);
            }

        } catch (Exception $exception) {
            report($exception);
        }
    }
}
