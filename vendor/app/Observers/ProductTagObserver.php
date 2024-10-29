<?php

namespace App\Observers;

use App\Models\ProductTag;

class ProductTagObserver
{
    /**
     * Handle the ProductTag "creating" event.
     */
    public function creating(ProductTag $productTag): void
    {
        $productTag->external_id = uniqid('PT');
    }

    /**
     * Handle the ProductTag "created" event.
     */
    public function created(ProductTag $productTag): void
    {
        //
    }

    /**
     * Handle the ProductTag "updated" event.
     */
    public function updated(ProductTag $productTag): void
    {
        //
    }

    /**
     * Handle the ProductTag "deleted" event.
     */
    public function deleted(ProductTag $productTag): void
    {
        //
    }

    /**
     * Handle the ProductTag "restored" event.
     */
    public function restored(ProductTag $productTag): void
    {
        //
    }

    /**
     * Handle the ProductTag "force deleted" event.
     */
    public function forceDeleted(ProductTag $productTag): void
    {
        //
    }
}
