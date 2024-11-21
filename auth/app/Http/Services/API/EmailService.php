<?php

namespace App\Http\Services\API;

use Exception;
use Illuminate\Support\Facades\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class EmailService
{
    public static function send(string $recipientEmail, string $message, string $subject)
    {
        logger('### SENDING EMAIL REQUEST TO KUDI-SMS API SERVICE ###');
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
                        'contents' => $recipientEmail
                    ],
                    [
                        'name' => 'subject',
                        'contents' => $subject
                    ],
                    [
                        'name' => 'message',
                        'contents' => $message
                    ]
                ]];
            $request = new Request('POST', env('KUDI_EMAIL_BASE_URL'));
            $res = $client->sendAsync($request, $options)->wait();

            logger('### EMAIL RESPONSE FROM KUDI-SMS SUCCESS ###');
            logger($res->getBody());

        } catch (Exception $exception) {
            report($exception);
        }
        return false;
    }
}
