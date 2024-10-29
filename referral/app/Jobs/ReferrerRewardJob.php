<?php

namespace App\Jobs;

use App\Models\Referral;
use App\Models\ReferralReward;
use App\Services\RewardService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ReferrerRewardJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */

    public function __construct(public ReferralReward $referralReward)
    {

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        logger('### REFERRER REWARD JOB DISPATCHED ###');

        try {
            match (strtolower($this->referralReward->referral->campaign->reward_type->type)) {
                Referral::REWARD_TYPE['CASH'] => (new RewardService($this->referralReward))->sendCashReward(),
                Referral::REWARD_TYPE['POINTS'] => (new RewardService($this->referralReward))->sendPoyntReward(),
                default => null
            };

        } catch (Exception $exception) {
            report($exception);
        }
    }
}
