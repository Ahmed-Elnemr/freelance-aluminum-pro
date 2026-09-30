<?php

namespace App\Mail;

class MailSmtpHost
{
    public static function resolve(?string $host): string
    {
        $normalizedHost = strtolower(trim((string) $host));

        if (in_array($normalizedHost, ['aluminumpro.online', 'mail.aluminumpro.online'], true)) {
            return 'aluminumpro.net';
        }

        if ($normalizedHost === '') {
            return '127.0.0.1';
        }

        return $normalizedHost;
    }
}
