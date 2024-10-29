<?php

namespace App\Listeners;

use App\Custom\Status;
use App\Events\SuccessfulOrderPlacementEvent;
use App\Http\Resources\OrderResource;
use App\Http\Services\GetAuthUserService;
use App\Http\Services\NotificationService;
use App\Models\Order;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Str;

class SuccessfulOrderPlacementListener
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
    public function handle(SuccessfulOrderPlacementEvent $event): void
    {
        logger('### SUCCESSFUL ORDER PLACEMENT EVENT TRIGGERED ###');
        $order = $event->order;
        try {
            self::notifyOnSuccessfulCollection($order, $order->user->external_id, self::CUSTOMER);
            self::notifyOnSuccessfulCollection($order, $order->vendor->user->external_id, self::VENDOR);

        } catch (Exception $exception) {
            report($exception);
        }
    }

    private static function notifyOnSuccessfulCollection(Order $order, string $userExternalId, string $notificationType): void
    {
        $user = GetAuthUserService::getUser($userExternalId);
        $firstName = $user['firstName'] ?? '';
        $numberOfPendingOrders = $order->vendor->orders()->where('status', '=', Status::PENDING)
            ->where('collection_status', '=', Status::SUCCESS)->count();

        $message = match ($notificationType) {
            self::CUSTOMER => "Thanks for your order $firstName. @{$order->vendor->shop_tag} is currently reviewing your order. We will let you know once that's done.\n\nKindly note that our delivery services will resume on the 8th of January. We will keep you updated on the status of your order.",
            default => "You have $numberOfPendingOrders pending " . Str::plural('order', $numberOfPendingOrders) . ". Kindly visit your shop to accept the order. It's not nice to keep your customer waiting.\n\nKindly note that our delivery services will resume on the 8th of January. We will keep you updated on the status of your order."
        };

        (new NotificationService([
            'userExternalId' => $userExternalId,
            'title' => 'Order Available',
            'body' => $message,
            'data' => json_encode(new OrderResource($order->load('orderCarts'))),
            'notificationAction' => $notificationType . ' order'
        ]))->sendPush();

        (new NotificationService([
            'userExternalId' => $userExternalId,
            'message' => $message
        ]))->sendSms();

    }
}
