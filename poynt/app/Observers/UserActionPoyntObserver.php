<?php

namespace App\Observers;

use App\Events\PoyntScoreNotificationEvent;
use App\Models\UserActionPoynt;

class UserActionPoyntObserver
{
    /**
     * Handle the UserActionPoynt "creating" event.
     */
    public function creating(UserActionPoynt $userActionPoynt): void
    {
        $userActionPoynt->external_id = uniqid();
    }

    /**
     * Handle the UserActionPoynt "created" event.
     */
    public function created(UserActionPoynt $userActionPoynt): void
    {
        event(new PoyntScoreNotificationEvent($userActionPoynt));
    }

    /**
     * Handle the UserActionPoynt "updated" event.
     */
    public function updated(UserActionPoynt $userActionPoynt): void
    {
        //
    }

    /**
     * Handle the UserActionPoynt "deleted" event.
     */
    public function deleted(UserActionPoynt $userActionPoynt): void
    {
        //
    }

    /**
     * Handle the UserActionPoynt "restored" event.
     */
    public function restored(UserActionPoynt $userActionPoynt): void
    {
        //
    }

    /**
     * Handle the UserActionPoynt "force deleted" event.
     */
    public function forceDeleted(UserActionPoynt $userActionPoynt): void
    {
        //
    }
}
