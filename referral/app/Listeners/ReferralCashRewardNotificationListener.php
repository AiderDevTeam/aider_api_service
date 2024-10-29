<?php

namespace App\Listeners;

use App\Events\ReferralCashRewardNotificationEvent;
use App\Http\Resources\ReferralRewardResource;
use App\Services\NotificationService;

class ReferralCashRewardNotificationListener
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
    public function handle(ReferralCashRewardNotificationEvent $event): void
    {
        logger('### REFERRAL CASH REWARD NOTIFICATION EVENT TRIGGERED ###');

        $referralReward = $event->referralReward;
        $referrerExternalId = $referralReward->referral->referrer_id;

        $rewardValue = number_format($referralReward->reward_value, 2);
        $referredUser = json_decode($referralReward->referral->referred->user_details, true);

        $message = "Here’s a gift of GHC $rewardValue to say thank you for referring {$referredUser['firstName']}.\nContinue to share your referral link with your friends and family.";

        (new NotificationService(['userExternalId' => $referrerExternalId, 'message' => $message]))->sendSms();

        (new NotificationService([
            'userExternalId' => $referrerExternalId,
            'title' => 'Referral Reward',
            'body' => $message,
            'data' => json_encode(new ReferralRewardResource($referralReward->load('referral'))),
            'notificationAction' => 'referral reward'
        ]))->sendPush();

        exit();
    }
}
