<?php

namespace App\Observers;

use App\Models\Size;

class SizeObserver
{
    /**
     * Handle the Size "creating" event.
     */
    public function creating(Size $size): void
    {
        $size->external_id = uniqid();
    }

    /**
     * Handle the Size "created" event.
     */
    public function created(Size $size): void
    {
        //
    }

    /**
     * Handle the Size "updated" event.
     */
    public function updated(Size $size): void
    {
        //
    }

    /**
     * Handle the Size "deleted" event.
     */
    public function deleted(Size $size): void
    {
        //
    }

    /**
     * Handle the Size "restored" event.
     */
    public function restored(Size $size): void
    {
        //
    }

    /**
     * Handle the Size "force deleted" event.
     */
    public function forceDeleted(Size $size): void
    {
        //
    }
}
