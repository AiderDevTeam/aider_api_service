<?php

namespace App\Http\Actions\Campaign;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class GetCampaignLeaderBoardAction{

    public function handle($id):JsonResponse{
        $users =  User::join('referral_user_numbers', 'referral_user_numbers.external_id', '=', 'users.external_id')
        ->where('referral_user_numbers.campaign_id', $id)
        ->selectRaw('users.*, CAST(referral_user_numbers.referrals_given AS UNSIGNED) AS ReferralsGiven, referral_user_numbers.referrals_given')
        ->orderBy('ReferralsGiven', 'DESC')
        ->groupBy('referral_user_numbers.referrals_given','users.external_id')
        ->with('referrals')
        ->get();
        return successfulJsonResponse(
            UserResource::collection($users)
        );
    }
}