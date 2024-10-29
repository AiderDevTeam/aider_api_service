<?php

namespace App\Observers;

use App\Events\UserIdentificationStatusUpdatedEvent;
use App\Models\UserIdentification;

class UserIdentificationObserver
{
    /**
     * Handle the UserIdentification "creating" event.
     */
    public function creating(UserIdentification $userIdentification): void
    {
        $userIdentification->external_id = uniqid('UID');
    }

    /**
     * Handle the UserIdentification "created" event.
     */
    public function created(UserIdentification $userIdentification): void
    {
        manuallySyncModels([$userIdentification->user]);
    }

    /**
     * Handle the UserIdentification "updated" event.
     */
    public function updated(UserIdentification $userIdentification): void
    {
        manuallySyncModels([$userIdentification->user]);

        if ($userIdentification->isDirty('status')) {
            event(new UserIdentificationStatusUpdatedEvent($userIdentification));
        }
    }

    /**
     * Handle the UserIdentification "deleted" event.
     */
    public function deleted(UserIdentification $userIdentification): void
    {
        //
    }

    /**
     * Handle the UserIdentification "restored" event.
     */
    public function restored(UserIdentification $userIdentification): void
    {
        //
    }

    /**
     * Handle the UserIdentification "force deleted" event.
     */
    public function forceDeleted(UserIdentification $userIdentification): void
    {
        //
    }
}
