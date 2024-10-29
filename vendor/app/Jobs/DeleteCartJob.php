<?php

namespace App\Jobs;

use App\Http\Resources\CartResource;
use App\Http\Services\ManuallySyncService;
use App\Models\Cart;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteCartJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $cartExternalIds;
    /**
     * Create a new job instance.
     */
    public function __construct($cartExternalIds)
    {
        $this->cartExternalIds = $cartExternalIds;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $deletedCarts = Cart::withTrashed()->whereIn('external_id', $this->cartExternalIds)->get();

        foreach ($deletedCarts as $deletedCart){
            $syncRequest = [
                'externalId' => $deletedCart->external_id,
                'collection' => 'carts',
                'data' => new CartResource(Cart::withTrashed()->where('external_id', $deletedCart->external_id)->first())
            ];
            ManuallySyncService::manualSync($syncRequest);
        }
    }

}
