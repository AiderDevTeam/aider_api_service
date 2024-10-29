<?php

namespace App\Observers;

use App\Models\BookingProductExchangeSchedule;

class BookingProductExchangeScheduleObserver
{
    /**
     * Handle the BookingProductExchangeSchedule "creating" event.
     */
    public function creating(BookingProductExchangeSchedule $bookingProductExchangeSchedule): void
    {
        $bookingProductExchangeSchedule->external_id = uniqid('BPES');
    }

    /**
     * Handle the BookingProductExchangeSchedule "created" event.
     */
    public function created(BookingProductExchangeSchedule $bookingProductExchangeSchedule): void
    {
        //
    }

    /**
     * Handle the BookingProductExchangeSchedule "updated" event.
     */
    public function updated(BookingProductExchangeSchedule $bookingProductExchangeSchedule): void
    {
        //
    }

    /**
     * Handle the BookingProductExchangeSchedule "deleted" event.
     */
    public function deleted(BookingProductExchangeSchedule $bookingProductExchangeSchedule): void
    {
        //
    }

    /**
     * Handle the BookingProductExchangeSchedule "restored" event.
     */
    public function restored(BookingProductExchangeSchedule $bookingProductExchangeSchedule): void
    {
        //
    }

    /**
     * Handle the BookingProductExchangeSchedule "force deleted" event.
     */
    public function forceDeleted(BookingProductExchangeSchedule $bookingProductExchangeSchedule): void
    {
        //
    }
}
