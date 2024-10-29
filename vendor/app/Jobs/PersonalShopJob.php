<?php

namespace App\Jobs;

use App\Http\Services\GetAuthUserService;
use App\Models\User;
use App\Models\Vendor;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PersonalShopJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            logger()->info('### RUNNING PERSONAL SHOP JOB ###');

            User::query()->chunk(5, function ($users) {
                foreach ($users as $user) {
                    logger('User:::::', [$user->external_id]);

                    if ($user->vendor->count() < 1)
                        continue;

                    $authUser = GetAuthUserService::getUser($user->external_id);
                    sleep(3);

                    if ($user->vendor()->where('shop_tag', $authUser['username'])->exists())
                        continue;

                    $firstShopAddress = $user->vendor()->first()?->address;

                    logger('### CREATING PERSONAL SHOP FOR USER ###', [$user->external_id]);
                    $vendor = $user->vendor()->createQuietly([
                        'external_id' => uniqid('V'),
                        'shop_tag' => $authUser['username'],
                        'shop_logo_url' => $authUser['profilePhotoUrl'],
                        'default' => true]);

                    $vendor->address()->createQuietly([
                        'city' => $firstShopAddress->city ?? 'Accra',
                        'state' => $firstShopAddress->state ?? 'Greater Accra',
                        'location_response' => $firstShopAddress->locationResponse ?? '',
                        'longitude' => $firstShopAddress->longitude ?? 0,
                        'latitude' => $firstShopAddress->latitude ?? 0,
                        'origin_name' => $firstShopAddress->originName ?? ''
                    ]);
                    manuallySyncModels([$vendor]);
                }
                logger('### JOB TO  CREATE PERSONAL SHOPS FOR VENDORS COMPLETED ###');

            });
        } catch (Exception $exception) {
            report($exception);
        }

    }

}
