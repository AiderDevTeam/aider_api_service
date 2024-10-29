<?php

namespace App\Listeners;

use App\Custom\Status;
use App\Events\ProductListingIncentiveEvent;
use App\Http\Services\Payment\PayoutWalletService;
use App\Jobs\ProductListingIncentiveJob;
use App\Models\Incentive;
use App\Models\Product;
use App\Models\Setting;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use function PHPUnit\Framework\isNull;

class ProductListingIncentiveListener
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
    public function handle(ProductListingIncentiveEvent $event): void
    {
        $product = $event->product;
        logger('### PRODUCT LISTING INCENTIVE EVENT TRIGGERED ###');
        try {

            if ($product->hasReceivedListingIncentive()) {
                logger('### INCENTIVE ALREADY GIVEN FOR THIS LISTING ###');
                return;
            }


            if (is_null($payoutWallet = self::payoutWallet($product))) {
                logger('## PAYOUT WALLET NOT FOUND FOR VENDOR ###');
                return;
            }

            if ($setting = Setting::findByType(Setting::TYPES['PRODUCT_LISTING_INCENTIVE'])) {

                $incentive = $product->incentive()->create([
                    'amount' => $setting->value,
                    'account_number' => $payoutWallet['data']['accountNumber'],
                    'sort_code' => $payoutWallet['data']['sortCode'],
                    'description' => Incentive::TYPES['PRODUCT_LISTING'],
                    'status' => Status::PENDING,
                ]);

                ProductListingIncentiveJob::dispatch($product, $incentive)->onQueue('high');
            } else
                logger('### SETTINGS FOR INCENTIVE NOT FOUND ###');

        } catch (Exception $exception) {
            report($exception);
        }
    }

    public static function payoutWallet(Product $product)
    {
        $payoutWallet = PayoutWalletService::getWallet($product->vendor?->user->external_id);
        if (!is_null($payoutWallet) && isset($payoutWallet['data']['accountNumber'])) {
            return $payoutWallet;
        }
        return null;
    }
}
