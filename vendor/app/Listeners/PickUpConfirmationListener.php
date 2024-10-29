<?php

namespace App\Listeners;

use App\Events\PickUpConfirmationEvent;
use App\Http\Resources\ConversationResource;
use App\Http\Services\NotificationService;
use App\Models\Booking;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use PhpParser\Node\Scalar\String_;

class PickUpConfirmationListener
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
    public function handle(PickUpConfirmationEvent $event): void
    {
        logger('### PICKUP CONFIRMATION EVENT TRIGGERED ###');

        $booking = $event->booking;

        $this->notifyOnPickup($booking);
    }

    private function notifyOnPickup(Booking $booking): void
    {
        if (is_null($message = $this->notificationMessage($booking)))
            return;

        $userExternalId = $booking->isDirty('vendor_pickup_status') ? $booking->user->external_id : $booking->vendor->external_id;

        (new NotificationService([
            'userExternalId' => $userExternalId,
            'title' => 'Pickup Alert',
            'body' => $message,
            'data' => null,
            'notificationAction' => 'rentals'
        ]))->sendPush();
    }

    private function notificationMessage(Booking $booking): ?string
    {
        if ($booking->isDirty('vendor_pickup_status') && $booking->vendorPickupIsSuccessful()) {
            return $booking->vendor->details['firstName'] . ' has confirmed pickup';
        } else if ($booking->isDirty('user_pickup_status') && $booking->userPickupIsSuccessful()) {
            return $booking->user->details['firstName'] . ' has confirmed pickup';
        }
        return null;
    }
}
