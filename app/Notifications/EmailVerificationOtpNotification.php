<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailVerificationOtpNotification extends Notification
{
    use Queueable;

    public $otp;

    /**
     * Create a new notification instance.
     */
    public function __construct($otp)
    {
        $this->otp = $otp;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject(__('auth.Email Verification OTP'))
                    ->line(__('auth.Your OTP for email verification is: :otp', ['otp' => $this->otp]))
                    ->line(__('auth.This OTP is used to verify your new email address.'))
                    ->line(__('auth.It will expire in 15 minutes.'));
    }
}
