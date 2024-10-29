<?php

namespace App\Listeners;

use App\Events\UpdateVendorUserEvent;
use App\Http\Services\Vendor\VendorUserService;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Tymon\JWTAuth\Facades\JWTAuth;

class UpdateVendorUserListener
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
    public function handle(UpdateVendorUserEvent $event): void
    {
        logger('### UPDATE VENDOR USERNAME EVENT TRIGGERED ###');
        try {
            (new VendorUserService($event->user))->sendAuthUserToVendor();
        } catch (Exception $exception) {
            report($exception);
        }
    }
}
