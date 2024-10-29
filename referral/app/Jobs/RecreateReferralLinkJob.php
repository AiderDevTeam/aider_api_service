<?php

namespace App\Jobs;

use App\Events\GetReferralLinkEvent;
use App\Models\Campaign;
use App\Models\User;
use App\Models\UserReferralCampaign;
use App\Services\GoogleDynamicLinksService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RecreateReferralLinkJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $this->runProcess();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
       $this->runProcess();
    }

    private function runProcess(){
        User::all()->each(function($localUser){
            $campaigns = Campaign::all();
            foreach($campaigns as $campaign){
                $userCampaign = UserReferralCampaign::where("campaign_id", $campaign->id)->where("user_id", $localUser->id)->first();
                if($userCampaign){
                    $referral = GoogleDynamicLinksService::getDynamicLink($localUser->referral_no, $campaign->id, $localUser);
                    if(isset($referral['shortLink'])){
                        $extras = ['link' => $referral['shortLink'], 'campaignId' =>  $campaign->id];
                        //get dynamic link and store 
                        UserReferralCampaign::updateOrCreate([
                            'user_id' => $localUser->id,
                            'campaign_id' => $campaign->id
                         ],[
                            'user_id' => $localUser->id,
                            'campaign_id' => $campaign->id,
                            'referral_no'=> $localUser->referral_no,
                            'referral_url' => $extras['link']
                         ]);  
                    }
                }   
            }        
        });
    }
}
