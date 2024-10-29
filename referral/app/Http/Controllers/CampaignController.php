<?php

namespace App\Http\Controllers;

use App\Http\Actions\Campaign\GetCampaignChannelsAction;
use App\Http\Actions\Campaign\GetCampaignAction;
use App\Http\Actions\Campaign\GetCampaignLeaderBoardAction;
use App\Http\Actions\Campaign\GetCampaignRewardTypeAction;
use App\Http\Actions\Campaign\GetCampaignRewardSplitAction;
use App\Http\Actions\Campaign\GetCampaignTypeAction;
use App\Http\Actions\Campaign\GetRunningCampaignAction;
use App\Http\Actions\Campaign\StoreCampaignAction;
use App\Http\Requests\CampaignRequest;
use App\Http\Resources\CampaignResource;
use App\Jobs\RecreateReferralLinkJob;
use App\Models\Campaign;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CampaignController extends Controller
{

    public function index( GetCampaignAction $action){
        return $action->handle();
    }
 
    /**
     * Stores a campaign based on the type, reward and referral type
     *
     * @param CampaignRequest $request
     * @param StoreCampaignAction $action
     * @return void
     */
    public function addCampaign(CampaignRequest $request, StoreCampaignAction $action)
    {
        return $action->handle($request);
    }

   
    /**
     * Retrieves Campaign Types
     *
     * @param GetCampaignTypeAction $action
     * @return void
     */
    public function getCampaignTypes(GetCampaignTypeAction $action)
    {
        return $action->handle();
    }

    /**
     * Retrieves Campaign Reward Types
     *
     * @param GetCampaignRewardTypeAction $action
     * @return void
     */
    public function getCampaignRewardTypes(GetCampaignRewardTypeAction $action)
    {
        return $action->handle();
    }


     /**
     * Retrieves Campaign Reward Splits
     *
     * @param GetCampaignRewardSplitAction $action
     * @return void
     */
    public function getCampaignRewardSplits(GetCampaignRewardSplitAction $action)
    {
        return $action->handle();
    }

    /**
     * Get Campaign channels
     *
     * @param GetCampaignChannelsAction $action
     * @return void
     */
    public function getCampaignChannels(GetCampaignChannelsAction $action){
        return $action->handle();
    }
    /**
     * Gets Leaderboard based on passed campaign id
     *
     * @param Request $request
     * @param [type] $id
     * @param GetCampaignLeaderBoardAction $action
     * @return void
     */
    public function getCampaignLeaderBoard(Request $request, $id, GetCampaignLeaderBoardAction $action){
        return $action->handle($id);
    }

    /**
     * Get Running Campaign
     *
     * @param Request $request
     * @param GetRunningCampaignAction $action
     * @return void
     */
    public function getRunningCampaign(Request $request, GetRunningCampaignAction $action){
        return $action->handle();
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * runs particular jobs once
     */
    public function runCampaignJobs(Request $request)
    {
        RecreateReferralLinkJob::dispatch();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Campaign $campaign)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Campaign $campaign)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Campaign $campaign)
    {
        //
    }

    public function getTestRunningCampaign(Request $request){
        $campaign = Campaign::where('end_date', '>=', Carbon::now()->toDateString())
        //->where('running','true')
       ->where('campaign_code', '=', 'black_tickets')
        ->first();

        if($campaign){
            return  successfulJsonResponse(new CampaignResource($campaign));
        }else{
            return errorJsonResponse(message: 'No running campaign', statusCode: 401);
        }
    }
}
