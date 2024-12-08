<?php

namespace App\Observers;

use App\Events\ProductListingIncentiveEvent;
use App\Events\ProductRejectionEvent;
use App\Events\ProductUpdatedEvent;
use App\Http\Services\AdminNotificationService;
use App\Jobs\CustomJobs\AddNewProductToColorboxSubShopsJob;
use App\Jobs\CustomJobs\UpdateColorboxSubShopProductsJob;
use App\Models\Product;
use App\Models\Vendor;

class ProductObserver
{
    protected $notificationService;

    public function __construct(AdminNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
    /**
     * Handle the Product "creating" event.
     */
    public function creating(Product $product): void
    {
        $product->external_id = uniqid('P');
    }

    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        $product->recordItemListedCount();
        // $this->notificationService->sendNotification('productListed');
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        $changes = $product->getChanges();

        if ($product->isDirty('status')) {

            if ($changes['status'] === Product::INACTIVE) {
                $product->recordDelistedItemCount();
                event(new ProductRejectionEvent($product));
            }

//            if ($changes['status'] === Product::ACTIVE) {
//                event(new ProductListingIncentiveEvent($product));
//            }
        }

//        event(new ProductUpdatedEvent($product));

    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
//        $product->recordDelistedItemCount();
    }

    /**
     * Handle the Product "restored" event.
     */
    public function restored(Product $product): void
    {
        //
    }

    /**
     * Handle the Product "force deleted" event.
     */
    public function forceDeleted(Product $product): void
    {
        //
    }
}
