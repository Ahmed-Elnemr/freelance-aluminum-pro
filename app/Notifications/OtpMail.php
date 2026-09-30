<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;

class OtpMail
{
    public static function make(string $otp, string $subject, string $intro): MailMessage
    {
        $appUrl = rtrim((string) config('app.url'), '/');

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.otp', [
                'subjectLine' => $subject,
                'heading' => $subject,
                'intro' => $intro,
                'otp' => $otp,
                'expires' => __('auth.It will expire in 15 minutes.'),
                'ignore' => __('auth.otp_email_ignore'),
                'logoUrl' => $appUrl.'/images/pwa/icon-512.png',
                'appName' => (string) config('app.name'),
                'appUrl' => $appUrl,
                'appHost' => (string) parse_url($appUrl, PHP_URL_HOST),
            ]);
    }
}
