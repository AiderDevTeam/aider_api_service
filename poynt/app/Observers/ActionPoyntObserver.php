<?php

namespace App\Observers;

use App\Models\ActionPoynt;

class ActionPoyntObserver
{
    /**
     * Handle the ActionPoynt "creating" event.
     */
    public function creating(ActionPoynt $actionPoynt): void
    {
        $actionPoynt->external_id = uniqid();
    }
    /**
     * Handle the ActionPoynt "created" event.
     */
    public function created(ActionPoynt $actionPoynt): void
    {
        //
    }

    /**
     * Handle the ActionPoynt "updated" event.
     */
    public function updated(ActionPoynt $actionPoynt): void
    {
        //
    }

    /**
     * Handle the ActionPoynt "deleted" event.
     */
    public function deleted(ActionPoynt $actionPoynt): void
    {
        //
    }

    /**
     * Handle the ActionPoynt "restored" event.
     */
    public function restored(ActionPoynt $actionPoynt): void
    {
        //
    }

    /**
     * Handle the ActionPoynt "force deleted" event.
     */
    public function forceDeleted(ActionPoynt $actionPoynt): void
    {
        //
    }
}
