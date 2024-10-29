<?php

namespace App\Listeners;

use App\Events\ReferralEvent;
use App\Models\Campaign;
use App\Models\ReferralUserNumber;
use App\Services\NotificationService as ServicesNotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ReferralListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ReferralEvent $event): void
    {
        try{
            //send to auth service

                $send = Http::withHeaders(jsonHttpHeaders())->post('http://auth/api/sys/user/grant-campaign-access/'.$event->referral->referred_id, [
                    "campaignType" => $event->referral->campaign->campaign_code
                ]);
                logger("SEND TO AUTH::");
                logger($send->json());
        }catch(\Exception $e){
            logger("ISSUE NOTIFYING REFERRAL ON AUTH::");
            logger($e->getMessage());
        }


        ReferralUserNumber::updateOrCreate([
            'external_id' => $event->referral->referrer_id
        ],[
            'external_id' => $event->referral->referrer_id,
            'campaign_id' => $event->referral->campaign_id,
            'referrals_given'=> DB::raw('referrals_given + 1')
        ]);

        try{
                //check the reward type and send notifications
                $campaign = Campaign::find($event->referral->campaign_id);
                $pn = $sms = "";
                if( $campaign->reward_type->name=="Cash" ){
                    //send a disbursement to referrer via payment api
                    logger('### Send Reward Notification For Cash :::');
                    logger(json_decode($event->referral->referrer->user_details)->firstName . " just referred " . json_decode($event->referral->referred->user_details)->firstName);
                    $cash = "(".$event->referral->campaign->cashPerPerson.")" ?? "";
                    $pn = json_decode($event->referral->referred->user_details)->firstName. " has been referred by you successfully, you will receive a reward in cash$cash in your wallet soon, if you don't have a wallet, create one now!";
                    $sms = json_decode($event->referral->referred->user_details)->firstName. " has been referred by you  successfully, you will receive a reward in cash$cash in your wallet soon, if you don't have a wallet, create one now!";

                }elseif($campaign->reward_type->name=="Points"){
                    $poynt = $event->referral->campaign->poyntPerPerson ?? "";
                    $pn = json_decode($event->referral->referred->user_details)->firstName. " has been referred by you successfully, you will receive  $poynt poynts to redeem for a gift on the Poynt App";
                    $sms = json_decode($event->referral->referred->user_details)->firstName. " has been referred by you  successfully, you will receive $poynt poynts to redeem for a gift on the Poynt App";

                }elseif($campaign->reward_type->name=="Discounts"){
                    $discount = "(".$event->referral->campaign->discountPerPerson.")" ?? "";
                    $pn = json_decode($event->referral->referred->user_details)->firstName. " has been referred by you successfully, you will receive $discount discounts in selected shops on Poynt";
                    $sms = json_decode($event->referral->referred->user_details)->firstName. " has been referred by you  successfully, you may receive $discount discounts in selected shops on Poynt";
                }else{
                    $pn = json_decode($event->referral->referred->user_details)->firstName. " has joined Poynt because of you☺️";
                    $sms = json_decode($event->referral->referred->user_details)->firstName. " has joined Poynt because of you☺️";
                }

                self::successfulReferralNotification(
                    json_decode($event->referral->referrer->user_details)->phone,
                    $pn,
                    $sms,
                    json_decode($event->referral->referrer->user_details)->externalId,
                    json_decode($event->referral->referrer->user_details)->firstName . " just referred " . json_decode($event->referral->referred->user_details)->firstName
                );

        }catch(\Exception $e){
            logger($e->getMessage());
        }

    }

    private static function successfulReferralNotification(string $destinationNumber, string $smsMessage, string $pushMessage, string $userExternalId, $pushData): void
    {
        //(new ServicesNotificationService(['phone' => $destinationNumber, 'message' => $smsMessage,]))->sendSms();

        (new ServicesNotificationService(['title' => 'Your friend is on POYNT!', 'body' => $pushMessage,
            'data' => $pushData, 'userExternalId' => $userExternalId]))->sendPush();
    }
}
