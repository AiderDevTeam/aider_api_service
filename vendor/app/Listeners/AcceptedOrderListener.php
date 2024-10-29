<?php

namespace App\Listeners;

use App\Events\AcceptedOrderEvent;
use App\Http\Services\GetAuthUserService;
use App\Http\Services\NotificationService;
use App\Jobs\ProcessDeliveryJob;
use App\Models\Order;
use App\Models\OrderLog;
use App\Models\User;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AcceptedOrderListener
{

    const CUSTOMER = 'customer';
    const VENDOR = 'vendor';

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
    public function handle(AcceptedOrderEvent $event): void
    {
        logger('### ORDER ACCEPTED EVENT TRIGGERED ###');
        $order = $event->order;

        try {
            ProcessDeliveryJob::dispatch($order);

            self::notifyOnOrderAcceptance($order, $order->user->external_id, self::CUSTOMER);
            self::notifyOnOrderAcceptance($order, $order->vendor->user->external_id, self::VENDOR);
            self::LogOrder($order);

        } catch (Exception $exception) {
            report($exception);
        }
    }

    public static function notifyOnOrderAcceptance(Order $order, string $userExternalId, string $notificationType): void
    {
        $user = User::findWithExternalId($userExternalId);
        $firstName = $user?->other_details['firstName'] ?? (explode(" ", $user?->full_name)[0] ?? '');



        $delivery = $order->delivery;
        $destination = $delivery->destination;

        $message = match ($notificationType) {
            self::CUSTOMER => "Hey $firstName!, @{$order->vendor->shop_tag}, has accepted your order. Kindly expect your package within 48 hours.",
            default => "Thanks for accepting the order. Remember to get the package ready for delivery with the customer details below.\nName : {$delivery->recipient->name}.\nLocation: {$destination->destination_name}, {$destination->city}.\nKindly note that we have collected the money from the customer and would credit your momo wallet once the customer has received the item."
        };

        (new NotificationService([
            'userExternalId' => $userExternalId,
            'title' => 'Order Accepted',
            'body' => $message,
            'data' => '',
            'notificationAction' => 'order'
        ]))->sendPush();

        (new NotificationService([
            'userExternalId' => $userExternalId,
            'message' => $message
        ]))->sendSms();
    }

    public function LogOrder(Order $order)
    {
        OrderLog::create([
            'order_number' => $order->order_number,
            'status' => $order->status,
            'accepted_date' => $order->updated_at,
            'accepted_by' => $order->vendor->user->full_name
        ]);
    }
}
