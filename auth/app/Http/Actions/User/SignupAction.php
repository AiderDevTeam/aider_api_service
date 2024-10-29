<?php

namespace App\Http\Actions\User;

use App\Http\Requests\UserSignupRequest;
use App\Http\Resources\UserResource;
use App\Http\Services\API\FileUploadService;
use App\Http\Services\UsernameSuggestionService;
use App\Jobs\CreateShopJob;
use App\Jobs\ReferralJob;
use App\Jobs\SetupPersonalShopJob;
use App\Models\User;
use App\Models\UserType;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

class SignupAction
{
    public function handle(UserSignupRequest $request): JsonResponse
    {
        logger('### USER SIGN UP INITIALIZED ###');
        logger($request->except('password'));
        try {

            DB::beginTransaction();

            if ($user = User::create(arrayKeyToSnakeCase($request->validated()))) {

                $user->refresh();

                $this->setupUserAddress($user, $request);

                $user->token = JWTAuth::fromUser($user);
                $user->expiresIn = auth()->factory()->getTTL() / 1440 . ' days';

                if ($request->has('userTypeId')) {
                    $user->setUserType($request->validated(['userTypeId']));
                }

                // $user->id_verification_status = 'completed';
                // $user->id_verified = true;
                // $user->id_verified_at = now();

                DB::commit();

                return successfulJsonResponse(new UserResource($user), statusCode: Response::HTTP_CREATED);
            }

        } catch (Exception $exception) {
            DB::rollBack();
            report($exception);
        }
        return errorJsonResponse();
    }

    private function setupUserAddress(User $user, UserSignupRequest $request): void
    {
        $user->addresses()->create([
            'longitude' => $request->address['longitude'],
            'latitude' => $request->address['latitude'],
            'origin_name' => $request->address['originName'],
            'city' => $request->address['city'],
            'country' => $request->address['country'],
            'country_code' => $request->address['countryCode'],
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'phone' => $user->phone,
            'calling_code' => $user->calling_code,
            'default' => true,
        ]);
    }

    private function setupShop(User $user): void
    {
        SetupPersonalShopJob::dispatchSync($user);
    }

    private function uploadProfilePhoto(UserSignupRequest $request): ?string
    {
        return FileUploadService::uploadToImageService(
            getLocalPath($request->file('profilePhoto'))
        );
    }

    private function checkUserReferral(UserSignupRequest $request, User $user): void
    {
        if ($request->has('referralLink') && !is_null($request->referralLink)) {
            ReferralJob::dispatch(['token' => $user->token,
                'campaignId' => $request->campaignId ?? null,
                'referredId' => $user->external_id,
                'referralLink' => $request->referralLink
            ])->onQueue('high');
        }
    }

    private function createShop(UserSignupRequest $request, User $user): void
    {
        if ($request->has('shopTag') && $request->filled('shopTag')) {

            $imageFileLocalPath = null;
            if ($request->has('shopLogo') && !is_null($request->file('shopLogo'))) {
                logger('shop logo available');
                $imageFileLocalPath = getLocalPath($request->file('shopLogo'));
            }

            $requestPayload = [
                'shopTag' => $request->shopTag,
                'accountNumber' => $request->accountNumber,
                'sortCode' => $request->sortCode,
                'accountName' => $request->accountName,
                'originName' => $request->shopOriginName,
                'city' => $request->shopCity,
                'state' => $request->shopState,
                'locationResponse' => $request->shopLocationResponse,
                'longitude' => $request->shopLongitude,
                'latitude' => $request->shopLatitude,
                'shopLogoFileType' => 'url'
            ];
            CreateShopJob::dispatch($requestPayload, $user, $imageFileLocalPath)->onQueue('high')->delay(now()->addSecond());
        }
    }
}
