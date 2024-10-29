<?php

namespace App\Listeners;

use App\Events\DropOffConfirmationEvent;
use App\Http\Services\NotificationService;
use App\Models\Booking;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DropOffConfirmationListener
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
    public function handle(DropOffConfirmationEvent $event): void
    {
        logger('### PICKUP CONFIRMATION EVENT TRIGGERED ###');

        $booking = $event->booking;

        $this->notifyOnDropOff($booking);
    }

    private function notifyOnDropOff(Booking $booking): void
    {
        if (is_null($message = $this->notificationMessage($booking)))
            return;

        $userExternalId = $booking->isDirty('vendor_drop_off_status') ? $booking->user->external_id : $booking->vendor->external_id;

        (new NotificationService([
            'userExternalId' => $userExternalId,
            'title' => 'Drop Off Alert',
            'body' => $message,
            'data' => null,
            'notificationAction' => 'rentals'
        ]))->sendPush();
    }

    private function notificationMessage(Booking $booking): ?string
    {
        if ($booking->isDirty('vendor_drop_off_status') && $booking->vendorDropOffIsSuccessful()) {
            return $booking->vendor->details['firstName'] . ' has confirmed drop off';
        } else if ($booking->isDirty('user_drop_off_status') && $booking->userDropOffIsSuccessful()) {
            return $booking->user->details['firstName'] . ' has confirmed drop off';
        }
        return null;
    }

}
