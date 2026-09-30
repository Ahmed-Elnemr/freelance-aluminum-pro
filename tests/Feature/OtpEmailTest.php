<?php

namespace Tests\Feature;

use App\Notifications\EmailVerificationOtpNotification;
use App\Notifications\ResetPasswordOtpNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Tests\TestCase;

class OtpEmailTest extends TestCase
{
    public function test_verification_email_uses_branded_template_with_logo_and_code(): void
    {
        app()->setLocale('ar');

        $message = (new EmailVerificationOtpNotification('8897'))->toMail(new \stdClass);

        $this->assertInstanceOf(MailMessage::class, $message);
        $this->assertSame('رمز التحقق من البريد الإلكتروني', $message->subject);

        $html = view($message->view, $message->viewData)->render();

        $this->assertStringContainsString('8897', $html);
        $this->assertStringContainsString('/images/pwa/icon-512.png', $html);
        $this->assertStringContainsString('dir="rtl"', $html);
        $this->assertStringNotContainsString('Hello!', $html);
    }

    public function test_reset_email_uses_english_layout_when_locale_is_english(): void
    {
        app()->setLocale('en');

        $message = (new ResetPasswordOtpNotification('4321'))->toMail(new \stdClass);
        $html = view($message->view, $message->viewData)->render();

        $this->assertStringContainsString('4321', $html);
        $this->assertStringContainsString('dir="ltr"', $html);
        $this->assertStringContainsString('Use the code below to reset your password.', $html);
    }
}
