<?php

namespace App\Listeners;

use App\Events\StoreRewardValueEvent;
use App\Jobs\CampaignImageUploadJob;
use App\Models\RewardValue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class StoreRewardValueListener
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
    public function handle(StoreRewardValueEvent $event): void
    {
        logger()->info('### STORE OR UPDATE REWARD');
       $store =  RewardValue::updateOrCreate([
                'campaign_id' => $event->campaign->id
            ],[
                'amount' => (float)$event->fullAmount,
                'point' => $event->fullPoint,
                'reward_type_id' => $event->campaign->reward_type_id
            ]);
        logger($store);
        logger()->info('### UPLOAD CAMPAIGN POSTERS');
        CampaignImageUploadJob::dispatch($event->campaign, $event->data);
        
    }
}
