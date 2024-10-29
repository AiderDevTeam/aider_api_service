<?php

namespace App\Listeners;

use App\Custom\Status;
use App\Events\IncentiveStatusChangeEvent;
use App\Http\Services\NotificationService;
use App\Models\Incentive;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class IncentiveStatusChangeListener
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
    public function handle(IncentiveStatusChangeEvent $event): void
    {
        logger('### INCENTIVE STATUS CHANGE EVENT TRIGGERED ###');
        try {
            $incentive = $event->incentive;

            if ($incentive->isSuccessful() && $incentive->isProductListingIncentive()) {
                self::notifyVendorOnSuccessfulIncentiveDisbursement($incentive);
            }

        } catch (Exception $exception) {
            report($exception);
        }
    }

    public static function notifyVendorOnSuccessfulIncentiveDisbursement(Incentive $incentive): void
    {
        $user = $incentive->incentivable->vendor->user;

        $sms = "Congratulations @{$incentive->incentivable->vendor->shop_tag}! You just earned GHC $incentive->amount for listing your product on Poynt. Continue to list more products and keep earning your well deserved commissions!";
        $pushNotification = "Check your momo wallet for your commission for listing your product!";

        (new NotificationService([
            'userExternalId' => $user->external_id,
            'message' => $sms
        ]))->sendSms();

        (new NotificationService([
            'userExternalId' => $user->external_id,
            'title' => 'List more, Earn More 🚀💸!',
            'body' => $pushNotification,
            'data' => '',
            'notificationAction' => 'listing incentive'
        ]))->sendPush();
    }
}
