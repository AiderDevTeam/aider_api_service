<?php

namespace App\Observers;

use App\Models\ProductAddress;

class ProductAddressObserver
{
    /**
     * Handle the ProductAddress "creating" event.
     */
    public function creating(ProductAddress $productAddress): void
    {
        $productAddress->external_id = uniqid('PA');
    }

    /**
     * Handle the ProductAddress "updated" event.
     */
    public function updated(ProductAddress $productAddress): void
    {
        //
    }

    /**
     * Handle the ProductAddress "deleted" event.
     */
    public function deleted(ProductAddress $productAddress): void
    {
        //
    }

    /**
     * Handle the ProductAddress "restored" event.
     */
    public function restored(ProductAddress $productAddress): void
    {
        //
    }

    /**
     * Handle the ProductAddress "force deleted" event.
     */
    public function forceDeleted(ProductAddress $productAddress): void
    {
        //
    }
}
