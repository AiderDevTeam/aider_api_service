<?php

namespace App\Listeners;

use App\Events\ProductUpdatedEvent;
use App\Models\Cart;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ProductUpdatedListener
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
    public function handle(ProductUpdatedEvent $event): void
    {
        $product = $event->product;

        $productUnitPrice = $product->getChanges()['unit_price'] ?? null;
        if ($productUnitPrice) {
            $this->unitPriceUpdate($product);
        }

        $quantity = $product->getChanges()['quantity'] ?? null;
        if ($quantity) {
            $this->quantityUpdate($product);
        }

    }

    public function unitPriceUpdate($product)
    {
        try {
            logger()->info('### UPDATING CART UNIT PRICES ###');

            $product->carts()->where('is_checked_out', '=', false)
                ->update([
                    'unit_price' => $product->unit_price,
                    'discounted_amount' => $product->unit_price
                ]);

            manuallySyncModels([$product->vendor]);
        } catch (Exception $exception) {
            report($exception);
        }
    }

    public function quantityUpdate($product)
    {
        try {
            logger()->info('### UPDATING CART QUANTITY PRICES ###');

            $product->carts()->where('is_checked_out', '=', false)
                ->where('quantity', '>', $product->quantity)
                ->update(['quantity' => $product->quantity]);

            manuallySyncModels([$product->vendor]);

        } catch (\Exception $exception) {
            report($exception);
        }

    }
}
