<?php

namespace App\Listeners;

use App\Events\MakeSubtractionOperationEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class MakeSubtractionOperationListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(MakeSubtractionOperationEvent $event): void
    {
        if($event->referralReward->referral->campaign->reward_type->type == "Cash"){
            $event->referralReward->referral->campaign->reward_value()->decrement(
                'amount', $event->referralReward->referral->campaign->cash_per_person
            );
        }else{
            $event->referralReward->referral->campaign->reward_value()->decrement(
                'point', $event->referralReward->referral->campaign->poynt_per_person
            );
        }
    }
}
