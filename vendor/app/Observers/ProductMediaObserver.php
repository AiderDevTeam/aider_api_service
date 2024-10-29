<?php

namespace App\Observers;

use App\Models\ProductMedia;

class ProductMediaObserver
{
    /**
     * Handle the ProductMedia "creating" event.
     */
    public function creating(ProductMedia $productMedia): void
    {
        $productMedia->external_id = uniqid('PM');
    }

    /**
     * Handle the ProductMedia "created" event.
     */
    public function created(ProductMedia $productMedia): void
    {
        manuallySyncModels([$productMedia->product, $productMedia->product->vendor]);
    }

    /**
     * Handle the ProductMedia "updated" event.
     */
    public function updated(ProductMedia $productMedia): void
    {
        manuallySyncModels([$productMedia->product, $productMedia->product->vendor]);
    }

    /**
     * Handle the ProductMedia "deleted" event.
     */
    public function deleted(ProductMedia $productMedia): void
    {
        
    }

    /**
     * Handle the ProductMedia "restored" event.
     */
    public function restored(ProductMedia $productMedia): void
    {
        //
    }

    /**
     * Handle the ProductMedia "force deleted" event.
     */
    public function forceDeleted(ProductMedia $productMedia): void
    {
        //
    }
}
