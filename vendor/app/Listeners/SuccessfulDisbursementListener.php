<?php

namespace App\Listeners;

use App\Events\SuccessfulDisbursementEvent;
use App\Http\Services\NotificationService;
use App\Http\Services\Payment\PayoutWalletService;
use App\Models\Order;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SuccessfulDisbursementListener
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
    public function handle(SuccessfulDisbursementEvent $event): void
    {
        logger('### SUCCESSFUL ORDER DISBURSEMENT EVENT TRIGGERED ###');
        try {
            self::notifyOnSuccessfulDisbursement($event->order);
        } catch (Exception $exception) {
            report($exception);
        }
    }

    public static function notifyOnSuccessfulDisbursement(Order $order): void
    {
        $vendorUserExternalId = $order->vendor->user->external_id;
        $payoutWallet = PayoutWalletService::getWallet($vendorUserExternalId);

        $accountNumber = isset($payoutWallet['data']['accountNumber']) ? substr($payoutWallet['data']['accountNumber'], -4) : '';
        $message = "Cashout season! The payment for your recent order has been completed. Your account ******$accountNumber, has been credited with GHC $order->disbursement_amount";

        (new NotificationService([
            'userExternalId' => $vendorUserExternalId,
            'message' => $message
        ]))->sendSms();

        (new NotificationService([
            'userExternalId' => $vendorUserExternalId,
            'title' => 'Successful Payout',
            'body' => $message,
            'data' => '',
            'notificationAction' => 'order'
        ]))->sendPush();
    }
}
