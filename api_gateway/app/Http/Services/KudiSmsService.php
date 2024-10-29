<?php

namespace App\Http\Services;

use App\Jobs\SmsJob;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class KudiSmsService extends SmsJob
{
    public function sendSms(): void
    {
        try {
            logger('### SENDING SMS REQUEST TO KUDI-SMS API SERVICE ###');
            $client = new Client();
            $options = [
                'multipart' => [
                    [
                        'name' => 'token',
                        'contents' => env('KUDISMS_API_KEY')
                    ],
                    [
                        'name' => 'senderID',
                        'contents' => 'Aidar'
                    ],
                    [
                        'name' => 'recipients',
                        'contents' => $this->phone
                    ],
                    [
                        'name' => 'message',
                        'contents' => $this->message
                    ]
                ]];
            $request = new Request('POST', env('KUDISMS_BASE_URL'));
            $res = $client->sendAsync($request, $options)->wait();

            logger('### RESPONSE FROM KUDI-SMS ###');
            logger($res->getBody());

        } catch (Exception $exception) {
            report($exception);
        }
    }
}
