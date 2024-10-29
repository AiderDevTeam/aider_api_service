<?php

namespace App\Listeners;

use App\Custom\Status;
use App\Events\DeliveryStatusUpdatesEvent;
use App\Http\Resources\OrderResource;
use App\Http\Services\GetAuthUserService;
use App\Http\Services\NotificationService;
use App\Jobs\OrderReversalJob;
use App\Models\Order;
use App\Models\OrderLog;
use App\Models\User;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DeliveryStatusUpdatesListener
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
    public function handle(DeliveryStatusUpdatesEvent $event): void
    {
        $order = $event->order;
        logger('### DELIVERY STATUS CHANGE EVENT TRIGGERED ###', [$order->status]);
        try {
            $this->initiateReversal($order);

            self::notifyOnDeliveryStatusChange($order, $order->user->external_id, self::CUSTOMER);
            self::notifyOnDeliveryStatusChange($order, $order->vendor->user->external_id, self::VENDOR);
            self::LogOrder($order);
        } catch (Exception $exception) {
            report($exception);
        }
    }

    public static function notifyOnDeliveryStatusChange(Order $order, string $userExternalId, string $notificationType): void
    {
        $user = User::findWithExternalId($userExternalId);
        $firstName = $user?->other_details['firstName'] ?? (explode(" ", $user?->full_name)[0] ?? '');

        $message = self::getMessage($order, $firstName, $notificationType);

        if (!is_null($message)) {
            (new NotificationService([
                'userExternalId' => $userExternalId,
                'title' => 'Order ' . ucfirst($order->status),
                'body' => $message,
                'data' => json_encode(new OrderResource($order->load('orderCarts'))),
                'notificationAction' => $notificationType . ' order status'
            ]))->sendPush();

            (new NotificationService([
                'userExternalId' => $userExternalId,
                'message' => $message
            ]))->sendSms();
        }

    }

    public static function getMessage(Order $order, string $customerFirstName, string $notificationType): ?string
    {
        $delivery = $order->delivery;
        $destination = $delivery->destination;

        return match ($order->status) {
            Status::DELIVERY_STATUS['ASSIGNED'] => match ($notificationType) {
                self::CUSTOMER => "Hey $customerFirstName, your package has been assigned to your rider. Remember to keep your phone close by so your rider can contact you.",
                default => "Your order has been assigned to a rider. Kindly ensure the package is properly labelled with the customer details below.\n\nName: {$delivery->recipient->name}\nLocation: $destination->destination_name, $destination->city"
            },
            Status::DELIVERY_STATUS['PICKED_UP'] => match ($notificationType) {
                self::CUSTOMER => "Hey $customerFirstName, your package has been picked up. Remember to keep your phone close by so your rider can contact you.",
                default => "Your package is on its way to your customer! We’ll let you know once it’s been delivered."
            },
            Status::DELIVERY_STATUS['TO_RECIPIENT'] => match ($notificationType) {
                self::CUSTOMER => "Hey $customerFirstName, your package is being delivered. Remember to keep your phone close by so your rider can contact you.",
                default => null
            },
            Status::DELIVERY_STATUS['SUCCESS'] => match ($notificationType) {
                self::CUSTOMER => "Your order is finally here $customerFirstName! We hope you love it! Share a picture of your order with your friends on your social media page to let them know you #ShopOnPoynt!\n\nTag @itspoynt on Instagram for a chance to win a gift from us!",
                default => "Your order has been successfully delivered. Kindly expect payment within 24 hours. Thanks for #SellingOnPoynt. Share a screenshot of your delivered status on instagram and tag @itspoynt to stand a chance to win a gift from us."
            },
            Status::DELIVERY_STATUS['CANCELED'] => match ($notificationType) {
                self::CUSTOMER => null,
                default => "Unfortunately delivery was unsuccessful. Your package will be returned to you shortly."
            },
            Status::DELIVERY_STATUS['REJECTED'] => match ($notificationType) {
                self::CUSTOMER => null,
                default => "Unfortunately, your package was rejected by the customer. It would be returned back to you."
            },
            Status::DELIVERY_STATUS['RETURNED'] => match ($notificationType) {
                self::CUSTOMER => null,
                default => "Hello @{$order->vendor->shop_tag}, unfortunately delivery was unsuccessful and the package has been returned to you."
            },
            default => null,
        };
    }

    public function LogOrder(Order $order): void
    {
        OrderLog::create([
            'order_number' => $order->order_number,
            'status' => $order->status,
        ]);
    }

    private function initiateReversal(Order $order): void
    {
        if ($order->isReversible())
            OrderReversalJob::dispatch($order)->onQueue('high');
    }

}
