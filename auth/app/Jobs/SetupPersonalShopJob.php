<?php

namespace App\Jobs;

use App\Http\Services\Vendor\ShopService;
use App\Models\User;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SetupPersonalShopJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private readonly User $user)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $address = $this->user->addresses?->first();
        try {
            (new ShopService(
                [
                    'shopTag' => $this->user->external_id,
                    'city' => $address->city ?? '',
                    'state' => $address->state ?? '',
                    'longitude' => $address->longitude ?? 0,
                    'latitude' => $address->latitude ?? 0,
                    'originName' => $address->destination_name ?? '',
                    'shopLogo' => $this->user->profile_photo_url,
                    'shopLogoFileType' => 'url'
                ],
                $this->user))->createShop();
        } catch (Exception $exception) {
            report($exception);
        }
    }
}
