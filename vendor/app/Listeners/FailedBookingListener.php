<?php

namespace App\Listeners;

use App\Events\FailedBookingEvent;
use App\Models\Booking;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class FailedBookingListener
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
    public function handle(FailedBookingEvent $event): void
    {
        $booking = $event->booking;
        $this->increaseProductQuantityOnFailedBooking($booking);
    }

    private function increaseProductQuantityOnFailedBooking(Booking $booking): void
    {
        logger('### INCREASING PRODUCT QUANTITY ON FAILED BOOKING ###');

        $bookingProduct = $booking->bookedProduct;
        $bookingProduct->product->increaseQuantity($bookingProduct->product_quantity);
    }
}
