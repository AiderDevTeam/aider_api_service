<?php

namespace App\Observers;

use App\Custom\Status;
use App\Events\AcceptedOrderEvent;
use App\Events\DeliveryStatusUpdatesEvent;
use App\Events\FailedCollectionEvent;
use App\Events\RejectedOrderEvent;
use App\Events\SuccessfulOrderPlacementEvent;
use App\Events\SuccessfulDisbursementEvent;
use App\Events\SuccessfulOrderReversalEvent;
use App\Http\Services\Api\DeliveryService;
use App\Jobs\ProcessDeliveryJob;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;

class OrderObserver
{
    /**
     * Handle the Order "creating" event.
     */
    public function creating(Order $order): void
    {
        $order->external_id = uniqid('O');
    }

    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        $changes = $order->getChanges();

        if ($order->isDirty('collection_status')) {
            match ($changes['collection_status']) {
                Status::SUCCESS => event(new SuccessfulOrderPlacementEvent($order)),
                Status::FAILED => event(new FailedCollectionEvent($order)),
                default => null
            };
        }

        if ($order->isDirty('status')) {

            if ($changes['status'] === Status::SUCCESS) {
                $order->recordItemsRentedCount();
            }

            match ($changes['status']) {
                Status::ACCEPTED => event(new AcceptedOrderEvent($order)),
                Status::DECLINED => event(new RejectedOrderEvent($order)),
                default => event(new DeliveryStatusUpdatesEvent($order))
            };
        }

        if ($order->isDirty('disbursement_status')) {
            if ($changes['disbursement_status'] === Status::SUCCESS) {
                event(new SuccessfulDisbursementEvent($order));
            }
        }

        if ($order->isDirty('reversal_status'))
            if ($changes['reversal_status'] === Status::SUCCESS) {
                event(new SuccessfulOrderReversalEvent($order));
            }
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "restored" event.
     */
    public function restored(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "force deleted" event.
     */
    public function forceDeleted(Order $order): void
    {
        //
    }
}
