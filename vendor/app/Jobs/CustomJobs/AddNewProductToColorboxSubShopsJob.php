<?php

namespace App\Jobs\CustomJobs;

use App\Models\Product;
use App\Models\Vendor;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AddNewProductToColorboxSubShopsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Product $mainShopProduct)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        logger('### ADDING NEW PRODUCTS TO COLORBOX SUB-SHOPS JOB --- STARTED ###');
        try {
            logger($this->mainShopProduct);

            if (strtolower($this->mainShopProduct->vendor->shop_tag) !== strtolower(COLORBOX['MAIN_SHOP'])) {
                logger('### NOT A COLORBOX PRODUCT ###');
                return;
            }

            if (!$colorboxMainShop = Vendor::findByTag(COLORBOX['MAIN_SHOP'])) {
                logger('### SHOP NOT FOUND ###');
                return;
            }

            //grab sub-shops without personalShop and MainShop
            $colorboxSubShops = $colorboxMainShop->user->vendor()->whereNotIn('shop_tag', array_values(COLORBOX))->get();

            foreach ($colorboxSubShops as $subShop) {

                logger("### CREATING PRODUCT FOR $subShop->shop_tag ###");

                $subShopProduct = $subShop->products()->create([
                    'sub_category_id' => $this->mainShopProduct->sub_category_id,
                    'name' => $this->mainShopProduct->name,
                    'description' => $this->mainShopProduct->name,
                    'condition' => $this->mainShopProduct->condition,
                    'size' => $this->mainShopProduct->size,
                    'color' => $this->mainShopProduct->color,
                    'quantity' => $this->mainShopProduct->quantity,
                    'weight' => $this->mainShopProduct->weight,
                    'weight_unit_id' => $this->mainShopProduct->weight_unit_id,
                    'unit_price' => $this->mainShopProduct->unit_price,
                    'status' => $this->mainShopProduct->status,
                    'discounted_price' => $this->mainShopProduct->discounted_price,
                    'approval_date' => $this->mainShopProduct->approval_date,
                    'rejection_date' => $this->mainShopProduct->rejection_date,
                    'reference_id' => $this->mainShopProduct->reference_id,
                ]);

                logger('### CREATING TAGS FOR PRODUCT ###');
                //create tags for sub shop product
                $tags = collect($this->mainShopProduct->tags->pluck('name'))->map(fn($tagName) => ['name' => $tagName]);//grab product tags of main product
                $subShopProduct->tags()->createMany($tags);

                logger('### CREATING PHOTOS FOR PRODUCT ###');
                //create pictures for sub shop products
                $photos = collect($this->mainShopProduct->photos->pluck('photoUrl'))->map(fn($photoUrl) => ['photoUrl' => $photoUrl]); //grab product photos of main product
                $subShopProduct->photos()->createMany($photos);

                logger('### SETTING SHARE LINK FOR PRODUCT ###');
                $subShopProduct->setShareLink(); //set share link for product
            }

        } catch (Exception $exception) {
            report($exception);
        }
        logger('### ADDING NEW PRODUCTS TO COLORBOX SUB-SHOPS JOB --- COMPLETED ###');
    }
}
