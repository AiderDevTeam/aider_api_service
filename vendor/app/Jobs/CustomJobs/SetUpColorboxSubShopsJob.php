<?php

namespace App\Jobs\CustomJobs;

use App\Custom\Status;
use App\Models\Product;
use App\Models\Vendor;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SetUpColorboxSubShopsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private readonly array $colorboxSubShops)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        logger()->info('### SETUP COLORBOX SUB-SHOPS JOB --- STARTED ###');
        try {
            if (!$colorboxMainShop = Vendor::findByTag(COLORBOX['MAIN_SHOP'])) {
                logger('### SHOP NOT FOUND ###');
                return;
            }
            $colorboxAccount = $colorboxMainShop?->user;
            logger($colorboxAccount);

            foreach ($this->colorboxSubShops as $colorboxSubShopName) {
                logger('### CREATING SUB-SHOP FOR COLOR BOX ###');

                $shopName = $colorboxSubShopName . '_' . COLORBOX['MAIN_SHOP'];

                if (Vendor::where('shop_tag', $shopName)->exists())
                    continue;

                //create sub shop
                $colorboxSubShop = $colorboxAccount->vendor()->create([
                    'shop_logo_url' => $colorboxMainShop->shop_logo_url,
                    'business_name' => $colorboxMainShop->business_name,
                    'shop_tag' => $shopName,
                    'default' => false,
                    'commission' => $colorboxMainShop->commission,
                    'insurance' => $colorboxMainShop->insurance,
                    'shop_logo_url_b' => $colorboxMainShop->shop_logo_url_b,
                    'shop_logo_url_b_status' => $colorboxMainShop->shop_logo_url_b_status,
                    'type' => $colorboxMainShop->type,
                    'official' => false,
                    'pay_on_delivery' => $colorboxMainShop->pay_on_delivery
                ]);

                if ($colorboxSubShop) {

                    $colorboxSubShop->address()->create([
                        'city' => $colorboxMainShop->address->city,
                        'state' => $colorboxMainShop->address->state,
                        'location_response' => $colorboxMainShop->address->location_response,
                        'longitude' => $colorboxMainShop->address->longitude,
                        'latitude' => $colorboxMainShop->address->latitude,
                        'origin_name' => $colorboxMainShop->address->origin_name
                    ]);

                    logger('### SETTING SHARE LINK FOR SUB_SHOP ###');
                    $colorboxSubShop->setShareLink();

                    $mainShopProducts = $colorboxMainShop->getAvailableProducts()
                        ->whereHas('photos')
                        ->whereIn('status', [Status::ACTIVE, Status::PENDING])
                        ->where('quantity', '>', 0)->get();

                    foreach ($mainShopProducts as $mainShopProduct) {
                        logger('### CREATING PRODUCT FOR SUB-SHOP FROM MAIN-SHOP PRODUCTS ###');
                        //create product for sub shop
                        $subShopProduct = $colorboxSubShop->products()->create([
                            'sub_category_id' => $mainShopProduct->sub_category_id,
                            'name' => $mainShopProduct->name,
                            'description' => $mainShopProduct->name,
                            'condition' => $mainShopProduct->condition,
                            'size' => $mainShopProduct->size,
                            'color' => $mainShopProduct->color,
                            'quantity' => $mainShopProduct->quantity,
                            'weight' => $mainShopProduct->weight,
                            'weight_unit_id' => $mainShopProduct->weight_unit_id,
                            'unit_price' => $mainShopProduct->unit_price,
                            'status' => $mainShopProduct->status,
                            'discounted_price' => $mainShopProduct->discounted_price,
                            'approval_date' => $mainShopProduct->approval_date,
                            'rejection_date' => $mainShopProduct->rejection_date,
                            'reference_id' => $mainShopProduct->reference_id,
                        ]);

                        logger('### CREATING TAGS FOR PRODUCT ###');
                        //create tags for sub shop product
                        $tags = collect($mainShopProduct->tags->pluck('name'))->map(fn($tagName) => ['name' => $tagName]);//grab product tags of main product
                        $subShopProduct->tags()->createMany($tags);

                        logger('### CREATING PHOTOS FOR PRODUCT ###');
                        //create pictures for sub shop products
                        $photos = collect($mainShopProduct->photos->pluck('photoUrl'))->map(fn($photoUrl) => ['photoUrl' => $photoUrl]); //grab product photos of main product
                        $subShopProduct->photos()->createMany($photos);

                        logger('### SETTING SHARE LINK FOR PRODUCT ###');
                        $subShopProduct->setShareLink(); //set share link for product
                    }
                }
            }

        } catch (Exception $exception) {
            logger([
                'message' => 'something went wrong',
                'error' => $exception->getMessage()
            ]);
            report($exception);
        }
        logger()->info('### SETUP COLORBOX SUB-SHOPS JOB --- COMPLETED ###');
    }
}
