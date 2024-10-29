<?php

namespace App\Observers;

use App\Models\DeliveryFee;

class DeliveryFeeObserver
{
    /**
     * Handle the DeliveryFee "creating" event.
     */
    public function creating(DeliveryFee $deliveryFee): void
    {
        $deliveryFee->external_id = uniqid('DF');
    }

    /**
     * Handle the DeliveryFee "created" event.
     */
    public function created(DeliveryFee $deliveryFee): void
    {
        //
    }

    /**
     * Handle the DeliveryFee "updated" event.
     */
    public function updated(DeliveryFee $deliveryFee): void
    {
        //
    }

    /**
     * Handle the DeliveryFee "deleted" event.
     */
    public function deleted(DeliveryFee $deliveryFee): void
    {
        //
    }

    /**
     * Handle the DeliveryFee "restored" event.
     */
    public function restored(DeliveryFee $deliveryFee): void
    {
        //
    }

    /**
     * Handle the DeliveryFee "force deleted" event.
     */
    public function forceDeleted(DeliveryFee $deliveryFee): void
    {
        //
    }
}
