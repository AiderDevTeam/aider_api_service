<?php

namespace App\Services;

use App\Events\MakeSubtractionOperationEvent;
use App\Models\ReferralReward;
use Exception;
use Illuminate\Support\Facades\Http;

class RewardService
{
    public function __construct(public ReferralReward $referralReward)
    {

    }

    public function sendCashReward(): void
    {
        try {
            logger('### SENDING REFERRER REWARD PAYOUT REQUEST TO API-GATEWAY SERVICE ###');
            logger($url = 'http://api-gateway/api/payments/disbursement');

            logger($data = [
                'transactionId' => $this->referralReward->external_id,
                'amount' => $this->referralReward->reward_value * 100,
                'rSwitch' => $this->referralReward->referrer_account_number_sort_code,
                'accountNumber' => $this->referralReward->referrer_account_number,
                'type' => 'momo disbursement',
                'description' => 'referral reward',
                'callbackUrl' => 'http://referral/webhooks/api-gateway-referral-reward-disbursement-response'
            ]);

            $response = Http::withHeaders(jsonHttpHeaders())->post($url, $data);

            if($response->successful()){
                try{
                    event(new MakeSubtractionOperationEvent( $this->referralReward ));
                }catch(\Exception $e){
                    logger('### RESPONSE FROM POYNT SERVICE FOR SUBTRACTION ERROE ###');
                    logger($e->getMessage());
                }
            }

            logger('### RESPONSE FROM API-GATEWAY SERVICE ###');
            logger($response);

        } catch (Exception $exception) {
            report($exception);
        }
    }

    public function sendPoyntReward(): bool
    {
        try {
            logger('### SENDING REFERRER REWARD POINTS REQUEST TO POYNT SERVICE ###');
            logger($url = 'http://poynt//api/user/update-poynt-balance/'. $this->referralReward->referral->referrer_id);

            $response = Http::withHeaders(jsonHttpHeaders())->post( $url, [
                'type' => 'credit',
                'action' => 'price reduction',
                'actionResponsePayload' => [],
                'actionValue' => $points ?? '200',
            ]);

            if($response->successful()){
                try{
                    event(new MakeSubtractionOperationEvent( $this->referralReward ));
                }catch(\Exception $e){
                    logger('### RESPONSE FROM POYNT SERVICE FOR SUBTRACTION ERROE ###');
                    logger($e->getMessage());
                }
            }

            logger('### RESPONSE FROM POYNT SERVICE ###');
            logger($response);

        } catch (Exception $exception) {
            report($exception);
            return false;
        }
        return true;

    }


}
