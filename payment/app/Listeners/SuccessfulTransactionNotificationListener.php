<?php

namespace App\Listeners;

use App\Events\SuccessfulTransactionNotificationEvent;
use App\Http\Resources\TransactionResource;
use App\Http\Services\NotificationService;
use App\Models\VASPayment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SuccessfulTransactionNotificationListener implements shouldQueue
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
    public function handle(SuccessfulTransactionNotificationEvent $event): void
    {
        logger('### SUCCESSFUL TRANSACTION NOTIFICATION TRIGGERED ###');
        $transaction = $event->transaction;
        $transactionType = $transaction->payment->type;

        $transactionDetails = new TransactionResource($transaction);

        match ($transactionType) {
            VASPayment::AIRTIME_TOP_UP => self::successfulVASPaymentNotification(
                $transaction->payment->destination_account_number,
                "You have just received an airtime top up of GHC {$transaction->payment->paymentable->value}. Keep an eye out for an added bonus – our way of saying thanks! Join the Poynt community to enjoy more of these discounts. Click this link to download. https://bit.ly/DownloadPoynt",
                "Your airtime gift of GHC {$transaction->payment->paymentable->value} has been delivered successfully. #StayOnPoynt for more exciting discounts!",
                $transaction->user->external_id,
                json_encode($transactionDetails),
            ),
            VASPayment::DATA_BUNDLE_PURCHASE => self::successfulVASPaymentNotification(
                $transaction->payment->destination_account_number,
                "You have just received a data bundle top up on {$transaction->payment->destination_account_number}. Keep an eye out for an added bonus – our way of saying thanks! Join the Poynt community to enjoy more of these discounts. Click this link to download. https://bit.ly/DownloadPoynt",
                "Your data bundle gift of GHC {$transaction->payment->paymentable->value} has been delivered successfully. #StayOnPoynt for more exciting discounts!",
                $transaction->user->external_id,
                $transactionDetails
            ),
            default => null
        };
    }

    private static function successfulVASPaymentNotification(string $destinationNumber, string $smsMessage, string $pushMessage, string $userExternalId, $pushData): void
    {
        (new NotificationService(['phone' => $destinationNumber, 'message' => $smsMessage,]))->sendSms();

        (new NotificationService(['title' => 'Streamed Successful', 'body' => $pushMessage,
            'data' => $pushData, 'userExternalId' => $userExternalId]))->sendPush();
    }

}
