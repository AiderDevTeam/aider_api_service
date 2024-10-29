<?php

namespace App\Observers;

use App\Models\ProductPhoto;

class ProductPhotoObserver
{
    /**
     * Handle the ProductPhoto "creating" event.
     */
    public function creating(ProductPhoto $productPhoto): void
    {
        $productPhoto->external_id = uniqid('PP');
    }

    /**
     * Handle the ProductPhoto "created" event.
     */
    public function created(ProductPhoto $productPhoto): void
    {
    }

    /**
     * Handle the ProductPhoto "updated" event.
     */
    public function updated(ProductPhoto $productPhoto): void
    {
    }

    /**
     * Handle the ProductPhoto "deleted" event.
     */
    public function deleted(ProductPhoto $productPhoto): void
    {
        //
    }

    /**
     * Handle the ProductPhoto "restored" event.
     */
    public function restored(ProductPhoto $productPhoto): void
    {
        //
    }

    /**
     * Handle the ProductPhoto "force deleted" event.
     */
    public function forceDeleted(ProductPhoto $productPhoto): void
    {
        //
    }
}
