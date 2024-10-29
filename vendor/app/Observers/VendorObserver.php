<?php

namespace App\Observers;

use App\Models\Vendor;

class VendorObserver
{
    /**
     * Handle the Vendor "creating" event.
     */
    public function creating(Vendor $vendor): void
    {
        $vendor->external_id = uniqid('V');
    }

    /**
     * Handle the Vendor "created" event.
     */
    public function created(Vendor $vendor): void
    {
        manuallySyncModels([$vendor->user]);
    }

    /**
     * Handle the Vendor "updated" event.
     */
    public function updated(Vendor $vendor): void
    {
        manuallySyncModels([$vendor->user]);
    }

    /**
     * Handle the Vendor "deleted" event.
     */
    public function deleted(Vendor $vendor): void
    {
        //
    }

    /**
     * Handle the Vendor "restored" event.
     */
    public function restored(Vendor $vendor): void
    {
        //
    }

    /**
     * Handle the Vendor "force deleted" event.
     */
    public function forceDeleted(Vendor $vendor): void
    {
        //
    }
}
