<?php

namespace App\Actions\Vendor;

use App\Http\Requests\StoreVendorRequest;
use App\Http\Resources\VendorResource;
use App\Http\Services\Api\UserService;
use App\Http\Services\GoogleDynamicLinksService;
use App\Jobs\FileUploadJob;
use App\Models\User;
use App\Models\Vendor;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Services\Api\WalletService;

class StoreVendorAction
{
    public function handle(Request $request, StoreVendorRequest $vendorRequest): JsonResponse
    {
        try {
            if (isset($request->user['externalId'])) {

                DB::beginTransaction();
                logger('### CREATING SHOP ###');
                $user = User::authUser($request->user);

                if (self::creatingSpace($request, $vendorRequest))
                    return successfulJsonResponse(data: (object)[], message: 'Space created', statusCode: 201);

                $vendor = $user->vendor()->updateOrCreate([
                    'shop_tag' => $vendorRequest['shopTag']
                ],
                    arrayKeyToSnakeCase($vendorRequest->validated())
                );

                self::setDefaultShop($request->user, $vendor);

                $vendor->address()->updateOrCreate([
                    'longitude' => $vendorRequest->longitude,
                    'latitude' => $vendorRequest->latitude
                ], [
                    'city' => $vendorRequest->city,
                    'state' => $vendorRequest->state ?? 'Greater Accra',
                    'location_response' => $vendorRequest->locationResponse ?? '',
                    'longitude' => $vendorRequest->longitude,
                    'latitude' => $vendorRequest->latitude,
                    'origin_name' => $vendorRequest->originName ?? ''
                ]);


                try {
                    if ($vendorRequest->has('categoriesIds')) {
                        $vendor->categories()->syncWithoutDetaching($vendorRequest->categoriesIds);
                    }

                    UserService::updateType($request);

                    if ($vendorRequest->has('accountNumber')) {
                        WalletService::create($request, $vendorRequest);
                    }
                } catch (Exception $e) {
                    logger('###ERROR CREATING WALLET::');
                    logger([
                        'request' => $request,
                        'vendorRequest' => $vendorRequest,
                        'error' => $e->getMessage()
                    ]);
                }

                if (!empty($vendorRequest->availabilities)) {
                    $vendorAvailabilities = collect($vendorRequest->availabilities)->map(function ($availability) {
                        return [
                            'day' => $availability['day'],
                            'opening_time' => Carbon::parse($availability['openingTime'])->toTimeString(),
                            'closing_time' => Carbon::parse($availability['closingTime'])->toTimeString(),
                        ];
                    })->toArray();
                    $vendor->availabilities()->createMany($vendorAvailabilities);
                }

                if ($vendorRequest->filled('shopLogo') && ($vendor->shop_tag !== $request->user['username'])) {
                    logger('### SHOP LOGO UPLOAD AND SET SHARE LINK JOB DISPATCHED ###');
                    FileUploadJob::dispatch($vendorRequest->validated(), $vendor)->onQueue('high');
                } else
                    $vendor->setShareLink();

                manuallySyncModels([$user]);
                DB::commit();

                logger('### SHOP CREATED ###');
                return successfulJsonResponse(
                    data: (new VendorResource($vendor)),
                    message: 'Vendor created successfully',
                    statusCode: 201
                );

            }

            return errorJsonResponse(message: 'User Authentication failed', statusCode: 401);

        } catch (Exception $exception) {
            DB::rollBack();
            report($exception);
        }
        return errorJsonResponse();
    }

    public static function setDefaultShop($user, Vendor $vendor): void
    {
        if ($user['username']) {
            if (strtolower($vendor->shop_tag) === strtolower($user['username']))
                $vendor->update(['default' => true, 'shop_logo_url' => $user['profilePhotoUrl']]);
        }
    }

    // this is for new users whose username has already been used by existing users to
    // create shop. these users cannot have personal shop
    public static function creatingSpace(Request $request, StoreVendorRequest $vendorRequest): bool
    {
        if ((strtolower($vendorRequest->shopTag) === strtolower($request->user['username']))
            && Vendor::where('shop_tag', '=', $vendorRequest->shopTag)->exists()) {

            if ($vendorRequest->has('accountNumber')) {
                logger('username is same as vendor name and it exist');
                WalletService::create($request, $vendorRequest);
            }
            return true;
        }
        return false;
    }

}
