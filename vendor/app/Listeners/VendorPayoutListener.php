<?php

namespace App\Listeners;

use App\Events\VendorPayoutEvent;
use App\Http\Services\Api\PaymentDisbursementService;
use App\Jobs\VendorPayoutJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class VendorPayoutListener
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
    public function handle(VendorPayoutEvent $event): void
    {
        logger('### VENDOR PAYOUT EVENT TRIGGERED ###');
        PaymentDisbursementService::process($event->delivery);
    }
}
