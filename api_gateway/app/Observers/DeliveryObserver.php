<?php

namespace App\Observers;

use App\Events\DeliveryUpdatedEvent;
use App\Models\Delivery;

class DeliveryObserver
{
    /**
     * Handle the Delivery "creating" event.
     */
    public function creating(Delivery $delivery): void
    {
        //
    }

    /**
     * Handle the Delivery "created" event.
     */
    public function created(Delivery $delivery): void
    {
    }

    /**
     * Handle the Delivery "updated" event.
     */
    public function updated(Delivery $delivery): void
    {
        if($delivery->isDirty(['status', 'tracking_number'])){
            event(new DeliveryUpdatedEvent($delivery));
        }
    }

    /**
     * Handle the Delivery "deleted" event.
     */
    public function deleted(Delivery $delivery): void
    {
        //
    }

    /**
     * Handle the Delivery "restored" event.
     */
    public function restored(Delivery $delivery): void
    {
        //
    }

    /**
     * Handle the Delivery "force deleted" event.
     */
    public function forceDeleted(Delivery $delivery): void
    {
        //
    }
}
