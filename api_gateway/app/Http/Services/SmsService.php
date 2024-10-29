<?php

namespace App\Http\Services;

class SmsService
{
    public static function send(string $to, string $message, ?string $from): bool
    {
        logger(config('app.aliases.bulk_sms_gateway'));

        match (config('app.aliases.bulk_sms_gateway')) {
            'kudi-sms' => KudiSmsService::dispatchSync($to, $message, $from),
            default => null
        };
        return true;
    }
}
