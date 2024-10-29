<?php

namespace App\Jobs;

use App\Models\Transaction;
use App\Models\User;
use App\Services\RewardService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendTransactionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $event;
    /**
     * Create a new job instance.
     */
    public function __construct($event)
    {
        //
        $this->event = $event;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
    }


    private function runRewardProc(){

        //check if referral campaign reward is cash then send cash

        $check =  $this->event->referral->campaign->reward_type->type ?? "";
        
        if($check == "cash"){
            $user = User::where('external_id', $this->event->referral->referrer_id)->first();
            //set amount from campaign creation, create migration and add it to validator
            $transaction = Transaction::create(
                [
                    'referrer_id',
                    'referred_id',
                    'amount',
                    'response_code',
                    'response_message',
                    'full_request',
                    'full_response',
                    'has_performed_transaction',
                    'campaign_id'
                ]
            );
             RewardService::sendCashReward($user, $transaction);
        }
    }

    private function checkIfUserIsEligible(){}
}
