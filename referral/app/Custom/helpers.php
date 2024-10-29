<?php

use App\Models\Campaign;
use App\Models\ReferralAllocation;
use App\Models\User;
use App\Models\UserReferralCampaign;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

function user(string $guard): Authenticatable
{
    return auth($guard)->user();
}

function successfulJsonResponse(mixed $data = [], string $message = 'Request processed successfully', $statusCode = 200): JsonResponse
{
    return response()->json([
        'success' => true,
        'message' => $message,
        'data' => $data
    ], $statusCode);
}

function errorJsonResponse(array $errors = [], string $systemError=null, string $message = 'Something went wrong, please try again later', $statusCode = 500): JsonResponse
{
    return response()->json([
        'success' => false,
        'message' => $message,
        'systemError'=>$systemError,
        'errors' => $errors
    ], $statusCode);
}
function toSnakeCase($data)
{
    $newdata = [];
    foreach ($data as $key => $value) {
        $keyValue = Str::snake($key);
        $newdata[$keyValue] = $value;
    }
    if (isset($data['userExternalId']) && $user = User::getByExternalId($data['userExternalId'])) $newdata['user_id'] = $user->id;
    return $newdata;
}


function toCamelCase($data)
{

    $newdata = [];
    foreach ($data as $key => $value) {
        $keyValue = Str::camel($key);
        $newdata[$keyValue] = $value;
    }

    return $newdata;
}



function getOrCreateUser($data): array
{
    logger('### Store a user');
    logger($data);
    $localUser = User::updateOrCreate([
        'external_id' => $data['externalId']
    ],[
        'external_id' => $data['externalId'],
        'points' => (isset($externalUser['userTypes'][0]) && in_array('ambassador',$externalUser['userTypes'])) ? env('INFLUENCER_REFERRAL_NO') : env('NORMAL_USER_REFERRAL_NO'),
        'user_details' => json_encode($data),
        'user_type' => (isset($externalUser['userTypes'][0]) && in_array('ambassador',$externalUser['userTypes'])) ? 'ambassador' :  'user',
        'referral_no' => Str::random(6)
     ]);
    logger($localUser);
    return $localUser->toArray();
}


function getCurrentUser(string $external_id){
    return User::where('external_id', $external_id)->first();
}


function getCurrentUserByReferralUrl(string $referral_url){
    $return = new \stdClass();
    //$referral_no = explode("?", $referral_url);

    $referral_no = explode("&", parse_url($referral_url, PHP_URL_QUERY));  
        if(count($referral_no) > 1){
            $code = str_replace( 'code=', '', $referral_no[0] ) ;

            if(!isset($referral_no[1])){
                return $return;
            }
            $code = str_replace( 'code=', '', $referral_no[0] ) ;
            $user = User::where('referral_no', $code)->first();
            if(empty($user)){
                $userReferralcampaign=UserReferralCampaign::where(
                    'referral_no',$code
                )->first();
            }else{
                $userReferralcampaign = UserReferralCampaign::where(
                    'user_id',$user->id
                )->first();
            }
            
        }else{
            $code = str_replace( 'code=', '', $referral_no[0] ) ;
            $user = User::where('referral_no', $code)->first();
            $userReferralcampaign = UserReferralCampaign::where(
                'user_id',$user->id
            )
            ->orWhere('referral_no',$code)
            ->first();
        }
   
    if($userReferralcampaign){
        $return->userReferralcampaign = $userReferralcampaign;
        $return->user = $userReferralcampaign->user;
    }else{
        $return->userReferralcampaign = null;
        $return->user = null;
    }
    return $return;
    

}

function getCampaign($id){
    return Campaign::find($id);
}

function getRunningCampaign(){
    return Campaign::where('end_date', '>=', Carbon::now()->toDateString())->where('running','true')->first();
}

function getReferralAllocation(string $campaign){
    return ReferralAllocation::where('campaign_id', $campaign)->first();
}

function toFloat($amount): float
{
    return stripCommas(
        number_format($amount, 2)
    );
}

function stripCommas(string $value): array|string
{
    return str_replace(',', '', $value);
}


function jsonHttpHeaders(): array
{
    return [
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ];
}

function manuallySyncModels(array $models): void
{
    if ($models){
        foreach ($models as $model) {
            logger()->info('### SYNCING MODEL: ' . class_basename($model) . ' ###');
            $model->syncData($model->external_id);
        }
    }
}

function getDestinationSortCode(string $destinationNumber): string
{
    return match (substr($destinationNumber, 0, 3)) {
        '020', '050' => 'VOD',
        '026', '056', '057', '027' => 'ATL',
        default => 'MTN'
    };
}
