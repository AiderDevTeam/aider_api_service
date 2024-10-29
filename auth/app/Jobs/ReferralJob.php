<?php

namespace App\Jobs;

use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class ReferralJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public array $data)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            logger('### HITTING ADD REFERRAL ON REFERRAL SERVICE ###');
            logger($url = 'http://referral/api/referrals/create');

            logger($data = [
                'campaignId' => $this->data['campaignId'],
                'referredId' => $this->data['referredId'],
                'referralLink' => $this->data['referralLink']
            ]);

            $response = Http::withToken($this->data['token'])->post($url, $data);
            logger('### RESPONSE FROM REFERRAL SERVICE ###');
            logger($response);

        } catch (Exception $exception) {
            report($exception);
        }
    }
}
