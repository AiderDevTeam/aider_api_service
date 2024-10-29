<?php

namespace App\Http\Controllers;

use App\Http\Actions\UserCampaignAccess\GrantUserCampaignAccessAction;
use App\Http\Requests\StoreUserCampaignAccessRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserCampaignAccessController extends Controller
{
    public function store(User $user, StoreUserCampaignAccessRequest $request, GrantUserCampaignAccessAction $action): JsonResponse
    {
        return $action->handle($user, $request);
    }

    public function getCampaignAccesses(User $user): JsonResponse
    {
        manuallySyncModels([$user]);
        return successfulJsonResponse($user->campaignAccesses->pluck('campaign_type'));
    }
}
