<?php

namespace Tests\Unit;

use App\Mail\MailFromAddress;
use PHPUnit\Framework\TestCase;

class MailFromAddressTest extends TestCase
{
    public function test_smtp_username_is_used_when_it_is_an_email(): void
    {
        $address = MailFromAddress::resolve(
            'mailbox@example.com',
            '_mainaccount@example.com',
        );

        $this->assertSame('mailbox@example.com', $address);
    }

    public function test_configured_from_address_is_used_when_username_is_not_an_email(): void
    {
        $address = MailFromAddress::resolve('cc038f96fac319', 'hello@example.com');

        $this->assertSame('hello@example.com', $address);
    }

    public function test_configured_from_address_is_used_when_username_is_missing(): void
    {
        $address = MailFromAddress::resolve(null, 'hello@example.com');

        $this->assertSame('hello@example.com', $address);
    }
}
