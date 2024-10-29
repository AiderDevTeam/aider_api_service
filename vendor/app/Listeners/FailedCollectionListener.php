<?php

namespace App\Listeners;

use App\Custom\Status;
use App\Events\FailedCollectionEvent;
use App\Http\Resources\OrderResource;
use App\Http\Services\NotificationService;
use App\Models\Order;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class FailedCollectionListener
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
    public function handle(FailedCollectionEvent $event): void
    {
        logger('### FAILED COLLECTION EVENT TRIGGERED ###');
        try {
            $order = $event->order;

            $order->increaseProductsQuantity();
            $this->reCreateCart($order);

            $this->notifyUserOnFailedCollection($order);
        } catch (Exception $exception) {
            report($exception);
        }
    }

    public function notifyUserOnFailedCollection(Order $order): void
    {
        $user = $order->user;
        if ($this->walletHasInsufficientFunds($order)) {
            $pushNotificationTitle = "Insufficient funds";
            $pushNotificationMessage = "Kindly confirm if you have GHC $order->amount_paid to complete your order";
            $smsMessage = sprintf(
                'Hey %s, we noticed you tried to make a purchase of GHC %s. Kindly top up your momo wallet to successfully complete your order.',
                $user->other_details['firstName'] ?? '',
                $order->amount_paid
            );

        } else {
            $pushNotificationTitle = 'Order Failed';
            $pushNotificationMessage = $smsMessage = 'Hi!, your order could not be completed due to failed payment.';
        }

        (new NotificationService([
            'userExternalId' => $user->external_id,
            'title' => $pushNotificationTitle,
            'body' => $pushNotificationMessage,
            'data' => json_encode(new OrderResource($order->load('orderCarts'))),
            'notificationAction' => 'customer order status'
        ]))->sendPush();

        (new NotificationService([
            'userExternalId' => $user->external_id,
            'message' => $smsMessage
        ]))->sendSms();
    }

    private function reCreateCart(Order $order): void
    {
        $cartsOrders = $order->carts();

        $cartsToCreate = collect($cartsOrders)->map(function ($cartItem) {
            return [
                'external_id' => uniqid('CA'),
                'unique_id' => setCartUniqueId($cartItem->user),
                'quantity' => $cartItem['quantity'],
                'vendor_id' => $cartItem['vendor_id'],
                'user_id' => $cartItem['user_id'],
                'product_id' => $cartItem['product_id'],
                'unit_price' => $cartItem['unit_price'],
                'discounted_amount' => $cartItem['discounted_amount'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];
        })->toArray();
        $order->user->carts()->createMany($cartsToCreate);
    }

    private function walletHasInsufficientFunds(Order $order): bool
    {
        try {
            logger('response payload', [$order->collection_response_payload]);

            if (is_null($responsePayload = $order->collection_response_payload))
                return false;

            $latestResponse = end($responsePayload);

            if (!isset($latestResponse['Data']) ||
                !isset($latestResponse['ResponseCode']) ||
                !isset($latestResponse['Data']['Description'])) {
                return false;
            }

            if ($latestResponse['ResponseCode'] === '2001' &&
                strpos($latestResponse['Data']['Description'], 'insufficient funds')) {
                return true;
            }
        } catch (Exception $exception) {
            report($exception);
        }
        return false;
    }
}
