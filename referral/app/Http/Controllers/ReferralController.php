<?php

namespace App\Http\Controllers;

use App\Http\Actions\Referral\GetReferralLinkAction;
use App\Http\Actions\Referral\GetUserReferralAction;
use App\Http\Actions\Referral\ReferralBalanceAction;
use App\Http\Actions\Referral\RewardReferrerAction;
use App\Http\Actions\Referral\StoreReferralAction;
use App\Http\Requests\GetReferralLinkRequest;
use App\Http\Requests\ReferralBalanceRequest;
use App\Http\Requests\ReferralRequest;
use App\Http\Requests\ReferralRewardRequest;
use App\Models\Referral;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function validateUserReferral(Request $request, GetUserReferralAction $action, $campaignId, $userExternalId)
    {
        return $action->handle($request,$campaignId, $userExternalId);
    }

    /**
     * Add a referral
     *
     * @param ReferralRequest $request
     * @param StoreReferralAction $action
     * @return JsonResponse
     */
    public function addReferral(ReferralRequest $request, StoreReferralAction $action): JsonResponse
    {
        return $action->handle($request);
    }
    /**
     * Get Referral Link
     *
     * @param GetReferralLinkRequest $request
     * @param GetReferralLinkAction $action
     * @return JsonResponse
     */
    public function getReferralLink(GetReferralLinkRequest $request, GetReferralLinkAction $action): JsonResponse{
        return $action->handle($request);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Referral $referral)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Referral $referral)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Referral $referral)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Referral $referral)
    {
        //
    }

    public function rewardReferrer(ReferralRewardRequest $referralRewardRequest, RewardReferrerAction $action): JsonResponse
    {
        return $action->handle($referralRewardRequest);
    }

    public function referralBalance(ReferralBalanceRequest $request, ReferralBalanceAction $action): JsonResponse
    {
        return $action->handle($request);
    }
}
