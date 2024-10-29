<?php

namespace App\Listeners;

use App\Events\PendingAcceptanceBookingsRecordingEvent;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class PendingAcceptanceBookingsRecordingListener
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
    public function handle(PendingAcceptanceBookingsRecordingEvent $event): void
    {
        logger('### PENDING ACCEPTANCE BOOKINGS COUNT RECORDING EVENT TRIGGERED ##');

        $booking = $event->booking;
        try {

            $booking->vendor->recordVendorBookingPendingAcceptance();
            manuallySyncModels([$booking->vendor]);

        } catch (Exception $exception) {
            report($exception);
        }
    }
}
