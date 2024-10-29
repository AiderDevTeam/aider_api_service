<?php

namespace App\Actions\Vendor\V2;

use App\Http\Requests\StoreVendorRequest;
use App\Http\Resources\VendorResource;
use App\Http\Services\Api\UserService;
use App\Http\Services\Api\WalletService;
use App\Jobs\SetShopLogoJob;
use App\Models\User;
use App\Models\Vendor;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CreateShopAction
{
    public function handle(Request $request, StoreVendorRequest $storeVendorRequest): JsonResponse
    {
        try {
            logger('### CREATING SHOP INITIALIZED ###');
            $requestPayload = $storeVendorRequest->validated();

            $user = User::authUser($request->user);

            $vendor = $user->vendor()->create(arrayKeyToSnakeCase($requestPayload));
            $vendor->address()->create([
                'city' => $requestPayload['city'],
                'state' => $requestPayload['state'],
                'location_response' => $requestPayload['locationResponse'],
                'longitude' => $requestPayload['longitude'],
                'latitude' => $requestPayload['latitude'],
                'origin_name' => $requestPayload['originName']
            ]);

//            UserService::updateType($request);

//            $this->createShopWallet($request, $storeVendorRequest);

//            $this->setShopAvailabilities($vendor, $storeVendorRequest);

//            $this->setShopLogo($vendor, $storeVendorRequest);

            return successfulJsonResponse(new VendorResource($vendor));

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }

    private function createShopWallet(Request $request, StoreVendorRequest $storeVendorRequest): void
    {
        try {
            if ($storeVendorRequest->has('accountNumber') && $storeVendorRequest->filled('accountNumber')) {
                WalletService::create($request, $storeVendorRequest);
            }
        } catch (Exception $exception) {
            logger()->info('### ERROR CREATING SHOP WALLET');
            logger(['error' => $exception->getMessage()]);
        }
    }

    private function setShopAvailabilities(Vendor $vendor, StoreVendorRequest $storeVendorRequest): void
    {
        if (!empty($storeVendorRequest->availabilities)) {
            $vendorAvailabilities = collect($storeVendorRequest->availabilities)->map(function ($availability) {
                return [
                    'day' => $availability['day'],
                    'opening_time' => Carbon::parse($availability['openingTime'])->toTimeString(),
                    'closing_time' => Carbon::parse($availability['closingTime'])->toTimeString(),
                ];
            })->toArray();
            $vendor->availabilities()->createMany($vendorAvailabilities);
        }
    }

    private function setShopLogo(Vendor $vendor, StoreVendorRequest $storeVendorRequest): void
    {
        logger('### SETTING SHOP LOGO ###');
        if ($vendor->isPersonalShop()) {
            $vendor->updateQuietly(['shop_logo_url' => $vendor->user->other_details['profilePhotoUrl']]);
            $vendor->setShareLink();
            return;
        }

        if ($storeVendorRequest->has('shopLogo')) {
            if ($storeVendorRequest->shopLogoFileType === SHOP_LOGO_FILE_TYPES['URL']) {
                $vendor->updateQuietly(['shop_logo_url' => $storeVendorRequest->shopLogo]);
                $vendor->setShareLink();
                return;
            }

            if ($storeVendorRequest->shopLogoFileType === SHOP_LOGO_FILE_TYPES['FORM_DATA_FILE']) {
                SetShopLogoJob::dispatch($vendor, getLocalPath($storeVendorRequest->file('shopLogo')))->onQueue('high')->delay(now()->addSecond());
                return;
            }
        }
        $vendor->setShareLink();
    }
}
