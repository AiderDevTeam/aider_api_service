<?php 

namespace App\Http\Actions\Campaign;

use App\Events\StoreRewardValueEvent;
use App\Http\Requests\CampaignRequest;
use App\Http\Resources\CampaignResource;
use App\Models\Campaign;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class StoreCampaignAction{

    public function handle(CampaignRequest $request): JsonResponse
    {
        try {
            logger()->info("### STORE CAMPAIGN ###");
            logger( $data = toSnakeCase($request->validated()));
            $campaign = Campaign::updateOrCreate(['name' => $data['name']],$data);
            $fullAmount = $data['full_amount'] ?? '';
            $fullPoint = $data['full_points'] ?? '';
            
            if($campaign){
                $allo = [
                    'campaign_id' => $campaign->id,
                    'ambassador' => $data['referral_allocation_for_ambassador'],
                    'user' => $data['referral_allocation_for_normal_user']
                ];
               $update = $campaign->referral_allocation()->update($allo);
                if(!$update){
                    $create = $campaign->referral_allocation()->create($allo);
                }
                event(new StoreRewardValueEvent($campaign,$fullAmount,$fullPoint, $data));
                return  successfulJsonResponse(new CampaignResource($campaign));
            }
            
            return errorJsonResponse(
                errors: ['Something Went Wrong'],
                message: 'Adding a campaign failed',
                statusCode: ResponseAlias::HTTP_UNAUTHORIZED);
            
        } catch (\Exception $exception) {
            return errorJsonResponse(
                errors: ['Something Went Wrong'],
                message: $exception->getMessage(),
                statusCode: ResponseAlias::HTTP_UNAUTHORIZED);
        }
        return errorJsonResponse();
    }
}