<?php

namespace App\Observers;

use App\Custom\Status;
use App\Events\FailedDeliveryEvent;
use App\Events\ReferralRewardEvent;
use App\Events\VendorPayoutEvent;
use App\Models\Delivery;

class DeliveryObserver
{
    /**
     * Handle the Delivery "created" event.
     */
    public function created(Delivery $delivery): void
    {
        //
    }

    /**
     * Handle the Delivery "updated" event.
     */
    public function updated(Delivery $delivery): void
    {
        $deliveryStatus = $delivery->getChanges()['status'] ?? null;

        if ($deliveryStatus == Status::SUCCESS) {
            event(new VendorPayoutEvent($delivery));
            event(new ReferralRewardEvent($delivery));
        }

        if ($deliveryStatus === Status::FAILED) {
            event(new FailedDeliveryEvent($delivery));
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
