<?php

namespace App\Http\Actions\UserCampaignAccess;

use App\Http\Requests\StoreUserCampaignAccessRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;

class GrantUserCampaignAccessAction
{
    public function handle(User $user, StoreUserCampaignAccessRequest $request): JsonResponse
    {
        logger('### GRANTING USER CAMPAIGN ACCESS ###');
        logger($request);

        try {
            $campaignType = $request->validated('campaignType');

            if (!$user->hasCampaignAccess($campaignType)) {
                $user->campaignAccesses()->create([
                    'external_id' => uniqid('CA'),
                    'campaign_type' => $campaignType
                ]);
            }
            manuallySyncModels([$user]);

            return successfulJsonResponse($user->campaignAccesses->pluck('campaign_type'));

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
