<?php

namespace App\Http\Services;

use App\Jobs\EmailJob;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class KudiEmailService extends EmailJob
{
    public function send()
    {
        try {

            logger('### SENDING EMAIL REQUEST TO KUDI-SMS API SERVICE ###');


            $client = new Client();
            $options = [
                'multipart' => [
                    [
                        'name' => 'token',
                        'contents' => env('KUDISMS_API_KEY')
                    ],
                    [
                        'name' => 'senderEmail',
                        'contents' => env('KUDI_SENDER_EMAIL')
                    ],
                    [
                        'name' => 'senderName',
                        'contents' => 'Aider'
                    ],
                    [
                        'name' => 'senderFrom',
                        'contents' => 'AIDER APP'
                    ],
                    [
                        'name' => 'transactionName',
                        'contents' => 'otp'
                    ],
                    [
                        'name' => 'recipient',
                        'contents' => $this->recipientEmail
                    ],
                    [
                        'name' => 'subject',
                        'contents' => $this->subject
                    ],
                    [
                        'name' => 'message',
                        'contents' => $this->message
                    ]
                ]];
            $request = new Request('POST', env('KUDI_EMAIL_BASE_URL'));
            $res = $client->sendAsync($request, $options)->wait();

            logger('### EMAIL RESPONSE FROM KUDI-SMS ###');
            logger($res->getBody());

        } catch (Exception $exception) {
            report($exception);
        }
    }
}
