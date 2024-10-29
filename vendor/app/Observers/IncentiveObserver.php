<?php

namespace App\Observers;

use App\Events\IncentiveStatusChangeEvent;
use App\Models\Incentive;

class IncentiveObserver
{
    /**
     * Handle the Incentive "creating" event.
     */
    public function creating(Incentive $incentive): void
    {
        $incentive->external_id = uniqid('IN');
    }

    /**
     * Handle the Incentive "created" event.
     */
    public function created(Incentive $incentive): void
    {
        //
    }

    /**
     * Handle the Incentive "updated" event.
     */
    public function updated(Incentive $incentive): void
    {
        if ($incentive->isDirty('status')) {
            event(new IncentiveStatusChangeEvent($incentive));
        }
    }

    /**
     * Handle the Incentive "deleted" event.
     */
    public function deleted(Incentive $incentive): void
    {
        //
    }

    /**
     * Handle the Incentive "restored" event.
     */
    public function restored(Incentive $incentive): void
    {
        //
    }

    /**
     * Handle the Incentive "force deleted" event.
     */
    public function forceDeleted(Incentive $incentive): void
    {
        //
    }
}
