<?php

namespace App\Providers;

use App\Models\Campaign;
use App\Models\User;
use App\Models\UserReferralCampaign;
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        
        Schema::defaultStringLength(191);
        //custom validations
        self::validationExtensions();
    }

    public static function validationExtensions(){
        Validator::extend('is_base64_image',function($attribute, $value, $params, $validator) {
           
            try {
              
                $image = base64_decode($value);
                $f = finfo_open();
                $result = finfo_buffer($f, $image, FILEINFO_MIME_TYPE);
                return ($result =='image/png' || $result =='image/jpg' || $result =='image/jpeg') ? true: false;   
            } catch (\Exception $e) {
                //throw $th;
                dd($e->getMessage());
            }
           
        });

        Validator::extend('has_campaign_expired',function($attribute, $value, $params, $validator) {
           
            try {
                return getRunningCampaign();
            } catch (\Exception $e) {
                //throw $th;
                dd($e->getMessage());
            }
           
        });

        
        Validator::extend('referral_link_exists',function($attribute, $value, $params, $validator) {
            try {
                //$referral_no = explode("&", $value);  
                $referral_no = explode("&", parse_url($value, PHP_URL_QUERY));  
                if(!isset($referral_no[0])){
                    return null;
                }           
                $code = str_replace( 'code=', '', $referral_no[0] ) ;
                $campaign = getRunningCampaign();
                $userReferralCamp = UserReferralCampaign::where(
                    'referral_no',$code
                )
                ->when($campaign, function($q) use($campaign){
                    return $q->where('campaign_id', $campaign->id);
                })
                ->first();
                $request = request();
                if($userReferralCamp){
                    $request['userReferralcampaign'] = $userReferralCamp;
                    $request['userData'] = $userReferralCamp->user;
                }
                return  $userReferralCamp;
            } catch (\Exception $e) {
                //throw $th;
                dd($e->getMessage());
            }
           
        });
    }
}
