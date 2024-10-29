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

class FixPersonalShopBugJob implements ShouldQueue
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
            User::query()->chunk(10, function ($users) {
                foreach ($users as $user) {
                    logger('User:::::', [$user->external_id]);

                    if ($user->vendor->count() < 1)//if user doesn't have any shop ignore
                        continue;

                    $authUser = GetAuthUserService::getUser($user->external_id);

                    if ($user->vendor()->where('shop_tag', $authUser['username'])->exists()) //if user already has personal shop ignore
                        continue;

                    //check if user's username has been used by another user to create shop
                    $otherShop = Vendor::where('shop_tag', $authUser['username']);
                    logger('other shop', [$otherShop->first()]);
                    logger('username', [$authUser['username']]);

                    if (!$otherShop->exists())//if no user has used the username to create a shop, ignore
                        continue;

                    $otherShop->first()?->update(['shop_tag' => $authUser['username'] . '_']);

                    //Create Personal Shop for this user
                    $shopAddress = $user->vendor()->first()?->address;

                    logger('### CREATING PERSONAL SHOP FOR USER ###', [$user->external_id]);
                    $vendor = $user->vendor()->createQuietly([
                        'external_id' => uniqid('V'),
                        'shop_tag' => $authUser['username'],
                        'shop_logo_url' => $authUser['profilePhotoUrl'],
                        'default' => true]);

                    $vendor->address()->createQuietly([
                        'city' => $shopAddress->city ?? 'Accra',
                        'state' => $shopAddress->state ?? 'Greater Accra Region',
                        'location_response' => $shopAddress->locationResponse ?? '',
                        'longitude' => $shopAddress->longitude ?? 0,
                        'latitude' => $shopAddress->latitude ?? 0,
                        'origin_name' => $shopAddress->originName ?? ''
                    ]);
                    manuallySyncModels([$vendor]);
                }
            });
        } catch (Exception $exception) {
            report($exception);
        }
    }
}
