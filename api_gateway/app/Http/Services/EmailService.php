<?php

namespace App\Http\Services;

class EmailService
{
    public static function send(string $recipientEmail, string $message, string $subject): bool
    {
        logger(config('app.aliases.email_gateway'));

        match (config('app.aliases.email_gateway')) {
            'kudi-email' => KudiEmailService::dispatchSync($recipientEmail, $message, $subject),
            default => null
        };
        return true;
    }
}
