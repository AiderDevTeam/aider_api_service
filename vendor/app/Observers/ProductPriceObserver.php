<?php

namespace App\Observers;

use App\Models\ProductPrice;

class ProductPriceObserver
{
    /**
     * Handle the ProductPrice "creating" event.
     */
    public function creating(ProductPrice $productPrice): void
    {
        $productPrice->external_id = uniqid();
    }

    /**
     * Handle the ProductPrice "created" event.
     */
    public function created(ProductPrice $productPrice): void
    {
        //
    }

    /**
     * Handle the ProductPrice "updated" event.
     */
    public function updated(ProductPrice $productPrice): void
    {
        //
    }

    /**
     * Handle the ProductPrice "deleted" event.
     */
    public function deleted(ProductPrice $productPrice): void
    {
        //
    }

    /**
     * Handle the ProductPrice "restored" event.
     */
    public function restored(ProductPrice $productPrice): void
    {
        //
    }

    /**
     * Handle the ProductPrice "force deleted" event.
     */
    public function forceDeleted(ProductPrice $productPrice): void
    {
        //
    }
}
