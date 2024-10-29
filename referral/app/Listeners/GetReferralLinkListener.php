<?php

namespace App\Listeners;

use App\Events\GetReferralLinkEvent;
use App\Models\Referral;
use App\Models\ReferralUserNumber;
use App\Models\UserReferralCampaign;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class GetReferralLinkListener
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
    public function handle(GetReferralLinkEvent $event): void
    {
        logger()->info('### GET REFERRAL EVENT');
        //save to  user referral campaign
         UserReferralCampaign::updateOrCreate([
            'user_id' => $event->localUser->id,
            'campaign_id' => $event->extras['campaignId']
         ],[
            'user_id' => $event->localUser->id,
            'campaign_id' => $event->extras['campaignId'],
            'referral_no'=> $event->localUser->referral_no,
            'referral_url' => $event->extras['link']
         ]);  
    }
}
