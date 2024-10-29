<?php

namespace App\Observers;

use App\Events\BookingAcceptanceStatusChangeEvent;
use App\Events\BookingCollectionStatusChangeEvent;
use App\Events\ConversationInitializationEvent;
use App\Events\DropOffConfirmationEvent;
use App\Events\FailedBookingEvent;
use App\Events\PendingAcceptanceBookingsRecordingEvent;
use App\Events\PickUpConfirmationEvent;
use App\Events\PendingPickupBookingsRecordingEvent;
use App\Events\SuccessfulBookingEvent;
use App\Models\Booking;

class BookingObserver
{
    /**
     * Handle the Booking "creating" event.
     */
    public function creating(Booking $booking): void
    {
        $booking->external_id = uniqid('B');
    }

    /**
     * Handle the Booking "created" event.
     */
    public function created(Booking $booking): void
    {
        event(new ConversationInitializationEvent($booking));
        event(new PendingAcceptanceBookingsRecordingEvent($booking));
    }

    /**
     * Handle the Booking "updated" event.
     */
    public function updated(Booking $booking): void
    {
        manuallySyncModels([$booking->message]);

        if ($booking->isDirty('booking_acceptance_status')) {
            event(new BookingAcceptanceStatusChangeEvent($booking));
            event(new PendingAcceptanceBookingsRecordingEvent($booking));
        }

        if ($booking->isDirty('collection_status')) {
            event(new BookingCollectionStatusChangeEvent($booking));
            event(new PendingPickupBookingsRecordingEvent($booking));
        }

        if ($booking->isDirty(['collection_status', 'booking_acceptance_status',
            'vendor_pickup_status', 'user_pickup_status',
            'vendor_drop_off_status', 'user_drop_off_status'])) {
            event(new SuccessfulBookingEvent($booking));
        }

        if ($booking->isDirty(['vendor_pickup_status', 'user_pickup_status'])) {
            event(new PickUpConfirmationEvent($booking));
            event(new PendingPickupBookingsRecordingEvent($booking));
        }

        if ($booking->isDirty(['vendor_drop_off_status', 'user_drop_off_status'])) {
            event(new DropOffConfirmationEvent($booking));
        }

        if ($booking->isDirty('status') && $booking->failed()) {
            event(new FailedBookingEvent($booking));
        }
    }

    /**
     * Handle the Booking "deleted" event.
     */
    public function deleted(Booking $booking): void
    {
        //
    }

    /**
     * Handle the Booking "restored" event.
     */
    public function restored(Booking $booking): void
    {
        //
    }

    /**
     * Handle the Booking "force deleted" event.
     */
    public function forceDeleted(Booking $booking): void
    {
        //
    }
}
