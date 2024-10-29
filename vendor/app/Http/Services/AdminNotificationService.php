<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Http;

class AdminNotificationService
{
    protected $baseUri;

    public function __construct()
    {
        $this->baseUri = '104.248.165.91:5001';
    }

    public function sendNotification($type)
    {
        logger('### NOTIFYING ADMIN ###');
        $response = Http::post($this->baseUri . '/api/notify-admin?type=' . $type);

        logger('NOTIFICATION RESPONSE::::', [$response->json()]);
        return $response->json();
    }
}
