<?php

namespace App\Observers;

use App\Events\ReferralEvent;
use App\Models\Referral;

class ReferralObserver
{
    /**
     * Handle the Referral "created" event.
     */
    public function created(Referral $Referral): void
    {
        event(new ReferralEvent($Referral));
        //manuallySyncModels([$Referral]);
    }

    /**
     * Handle the Referral "updated" event.
     */
    public function updated(Referral $Referral): void
    {
        //
        //manuallySyncModels([$Referral]);
    }

    /**
     * Handle the Referral "deleted" event.
     */
    public function deleted(Referral $Referral): void
    {
        //
    }

    /**
     * Handle the Referral "restored" event.
     */
    public function restored(Referral $Referral): void
    {
        //
    }

    /**
     * Handle the Referral "force deleted" event.
     */
    public function forceDeleted(Referral $Referral): void
    {
        //
    }
}
