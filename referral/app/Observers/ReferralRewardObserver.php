<?php

namespace App\Observers;

use App\Custom\Status;
use App\Events\ReferralCashRewardNotificationEvent;
use App\Jobs\ReferrerRewardJob;
use App\Models\ReferralReward;

class ReferralRewardObserver
{
    /**
     * Handle the ReferralReward "creating" event.
     */
    public function creating(ReferralReward $referralReward): void
    {
        $referralReward->external_id = uniqid('RR');
    }

    /**
     * Handle the ReferralReward "created" event.
     */
    public function created(ReferralReward $referralReward): void
    {
        ReferrerRewardJob::dispatch($referralReward);
    }

    /**
     * Handle the ReferralReward "updated" event.
     */
    public function updated(ReferralReward $referralReward): void
    {
        logger('referral reward observer');
        $rewardStatus = $referralReward->getChanges()['reward_status'] ?? null;

        if($rewardStatus && $rewardStatus == Status::SUCCESS){
            logger('checking observer');

            event(new ReferralCashRewardNotificationEvent($referralReward));
        }
    }

    /**
     * Handle the ReferralReward "deleted" event.
     */
    public function deleted(ReferralReward $referralReward): void
    {
        //
    }

    /**
     * Handle the ReferralReward "restored" event.
     */
    public function restored(ReferralReward $referralReward): void
    {
        //
    }

    /**
     * Handle the ReferralReward "force deleted" event.
     */
    public function forceDeleted(ReferralReward $referralReward): void
    {
        //
    }
}
