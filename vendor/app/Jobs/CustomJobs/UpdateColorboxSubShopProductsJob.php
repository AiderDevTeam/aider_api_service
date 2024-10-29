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
use Illuminate\Support\Facades\DB;

class UpdateColorboxSubShopProductsJob implements ShouldQueue
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
        $this->updateColorBoxProduct();
    }

    public function updateColorBoxProduct(): void
    {
        logger('### UPDATE COLORBOX SUB-SHOPS PRODUCTS JOB --- STARTED ###');
        try {

            if (strtolower($this->mainShopProduct->vendor->shop_tag) !== strtolower(COLORBOX['MAIN_SHOP'])) {
                logger('### NOT A COLORBOX PRODUCT ###');
                return;
            }

            if (!$colorboxMainShop = Vendor::findByTag(COLORBOX['MAIN_SHOP'])) {
                logger('### SHOP NOT FOUND ###');
                return;
            }

            if (is_null($this->mainShopProduct->reference_id)) {
                logger('### NULL REFERENCE ID NOT ALLOWED ###');
                return;
            }

            logger('### MAIN SHOP PRODUCT ###', [$this->mainShopProduct]);

            //grab sub-shops without personalShop and MainShop
            $colorboxSubShops = $colorboxMainShop->user->vendor()->whereNotIn('shop_tag', array_values(COLORBOX))->get();

            foreach ($colorboxSubShops as $subShop) {
                if ($subShopProduct = $subShop->products()->where('reference_id', $this->mainShopProduct->reference_id)->first()) {

                    logger('### UPDATING PRODUCT IN SUB-SHOP ###');
                    logger($subShop);
                    logger($subShopProduct);

                    $subShopProduct->update([
                        'name' => $this->mainShopProduct->name,
                        'description' => $this->mainShopProduct->description,
                        'unit_price' => $this->mainShopProduct->unit_price,
                        'discounted_price' => $this->mainShopProduct->discounted_price,
                        'quantity' => $this->mainShopProduct->quantity,
                        'status' => $this->mainShopProduct->status,
                        'size' => $this->mainShopProduct->size,
                    ]);
                }
            }

        } catch (Exception $exception) {
            report($exception);
        }

        logger('### UPDATE COLORBOX SUB-SHOPS PRODUCTS JOB --- COMPLETED ###');
    }

    public function readCSV()
    {
        $csv = array_map('str_getcsv', file(public_path('productUpdate.csv')));
        for ($x = 1; $x <= count($csv); $x++) {
            if (!empty($csv[$x][2])) {
                $data = [
                    // 'name' => $csv[$x][8] ?? "",
                    // 'description' => $csv[$x][21] ?? "",
                    // 'quantity' => $csv[$x][9] ?? "",
                    // 'size' => $csv[$x][17] ?? "",
                    // 'status' => $csv[$x][11] ?? "",
                    // 'unit_price' => $csv[$x][6]?? "",
                    // 'weight' => $csv[$x][20] ?? "",
                    // 'discounted_price' => (isset($csv[$x][0]) && !empty($csv[$x][0])) ? $csv[$x][0]:0.0,
                    // 'condition' => $csv[$x][5] ?? "",
                    // 'color' => $csv[$x][19] ?? "",
                    // 'weight_unit_id' => $csv[$x][14] ?? ""
                    //'external_id' => $csv[$x][2]
                    'sub_category_id' => $csv[$x][12] ?? ""
                ];
                try {
                    Product::where("external_id", $csv[$x][2])->update($data);
                } catch (\Exception $e) {
                    dd($e->getMessage(), $data);
                }

            }

        }
    }
}
