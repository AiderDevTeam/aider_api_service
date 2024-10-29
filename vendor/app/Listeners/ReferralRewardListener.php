<?php

namespace App\Listeners;

use App\Events\ReferralRewardEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;

class ReferralRewardListener
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
    public function handle(ReferralRewardEvent $event): void
    {
        try {
            logger('### REWARDING REFERER EVENT ##');
            $delivery = $event->delivery;
            $response = Http::withHeaders(jsonHttpHeaders())->post('http://referral/api/sys/reward-referrer', [
                'referredExternalId' => $delivery->order->user->external_id,
            ]);
            logger($response);

        } catch(\Exception $exception){
            report($exception);
        }
    }
}
