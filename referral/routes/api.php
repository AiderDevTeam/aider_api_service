<?php

use App\Http\Controllers\CampaignController;
use App\Http\Controllers\ReferralController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::middleware('auth.user')->group(function() {
    Route::get('/get/campaign/types', [CampaignController::class, 'getCampaignTypes']);
    Route::get('/get/campaign/reward/types', [CampaignController::class, 'getCampaignRewardTypes']);
    Route::get('/get/campaign/reward/splits', [CampaignController::class, 'getCampaignRewardSplits']);

});

Route::group(['middleware' => ['auth.user']], function() {
    Route::post('/campaigns/create', [CampaignController::class, 'addCampaign']);
    Route::get('/campaigns/channels', [CampaignController::class, 'getCampaignChannels']);
    Route::apiResource('/campaigns', CampaignController::class)->only('index', 'update');
    Route::get('/campaigns/leaderboard/{id}', [CampaignController::class, 'getCampaignLeaderBoard']);
    Route::get('/running/campaign', [CampaignController::class, 'getRunningCampaign']);
    Route::get('/run/campaign/jobs', [CampaignController::class, 'runCampaignJobs']);
    Route::get('/test/running/campaign', [CampaignController::class, 'getTestRunningCampaign']);
});

Route::group(['middleware' => ['auth.user']], function() {
    Route::post('/referrals/create', [ReferralController::class, 'addReferral']);
    Route::post('/referrals/get/link', [ReferralController::class, 'getReferralLink']);
    Route::post('/referrals/get/link', [ReferralController::class, 'getReferralLink']);

    Route::get('/referrals/verify/{campaignId}/{userExternalId}', [ReferralController::class, 'validateUserReferral']);


    Route::apiResource('/referrals', ReferralController::class)->only('index', 'update');

    Route::get('/referrals/balance', [ReferralController::class, 'referralBalance']);
});
