<?php

namespace App\Mail;

class MailFromAddress
{
    public static function resolve(?string $username, ?string $fromAddress): string
    {
        if (is_string($username) && filter_var($username, FILTER_VALIDATE_EMAIL)) {
            return $username;
        }

        if (is_string($fromAddress) && $fromAddress !== '') {
            return $fromAddress;
        }

        return 'hello@example.com';
    }
}
