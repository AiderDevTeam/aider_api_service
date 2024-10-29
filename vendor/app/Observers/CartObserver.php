<?php

namespace App\Observers;

use App\Models\Cart;

class CartObserver
{
    public function creating(Cart $cart): void
    {
        $cart->external_id = uniqid('CA');
    }

    /**
     * Handle the Cart "created" event.
     */
    public function created(Cart $cart): void
    {
        manuallySyncModels([$cart]);
    }

    /**
     * Handle the Cart "updated" event.
     */
    public function updated(Cart $cart): void
    {
        manuallySyncModels([$cart]);
    }

    /**
     * Handle the Cart "deleted" event.
     */
    public function deleted(Cart $cart): void
    {
        //
    }

    /**
     * Handle the Cart "restored" event.
     */
    public function restored(Cart $cart): void
    {
        //
    }

    /**
     * Handle the Cart "force deleted" event.
     */
    public function forceDeleted(Cart $cart): void
    {
        //
    }
}
