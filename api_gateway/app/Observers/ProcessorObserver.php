<?php

namespace App\Observers;

use App\Models\Processor;

class ProcessorObserver
{
    /**
     * Handle the Processor "creating" event.
     */
    public function creating(Processor $processor): void
    {
        $processor->external_id = uniqid();
    }

    /**
     * Handle the Processor "created" event.
     */
    public function created(Processor $processor): void
    {
        //
    }

    /**
     * Handle the Processor "updated" event.
     */
    public function updated(Processor $processor): void
    {
        //
    }

    /**
     * Handle the Processor "deleted" event.
     */
    public function deleted(Processor $processor): void
    {
        //
    }

    /**
     * Handle the Processor "restored" event.
     */
    public function restored(Processor $processor): void
    {
        //
    }

    /**
     * Handle the Processor "force deleted" event.
     */
    public function forceDeleted(Processor $processor): void
    {
        //
    }
}
