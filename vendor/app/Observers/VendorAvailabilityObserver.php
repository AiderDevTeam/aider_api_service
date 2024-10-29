<?php

namespace App\Observers;

use App\Models\VendorAvailability;

class VendorAvailabilityObserver
{
    /**
     * Handle the VendorAvailability "creating" event.
     */
    public function creating(VendorAvailability $vendorAvailability): void
    {
        $vendorAvailability->external_id = uniqid('VA');
    }

    /**
     * Handle the VendorAvailability "created" event.
     */
    public function created(VendorAvailability $vendorAvailability): void
    {
        //
    }

    /**
     * Handle the VendorAvailability "updated" event.
     */
    public function updated(VendorAvailability $vendorAvailability): void
    {
        //
    }

    /**
     * Handle the VendorAvailability "deleted" event.
     */
    public function deleted(VendorAvailability $vendorAvailability): void
    {
        //
    }

    /**
     * Handle the VendorAvailability "restored" event.
     */
    public function restored(VendorAvailability $vendorAvailability): void
    {
        //
    }

    /**
     * Handle the VendorAvailability "force deleted" event.
     */
    public function forceDeleted(VendorAvailability $vendorAvailability): void
    {
        //
    }
}
