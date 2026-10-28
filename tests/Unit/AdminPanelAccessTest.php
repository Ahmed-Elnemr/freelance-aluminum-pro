<?php

namespace Tests\Unit;

use App\Models\User;
use Filament\Panel;
use PHPUnit\Framework\TestCase;

class AdminPanelAccessTest extends TestCase
{
    public function test_only_an_active_admin_can_access_the_panel(): void
    {
        $panel = $this->createMock(Panel::class);

        $admin = new User;
        $admin->type = 'admin';
        $admin->is_active = true;

        $inactiveAdmin = new User;
        $inactiveAdmin->type = 'admin';
        $inactiveAdmin->is_active = false;

        $client = new User;
        $client->type = 'client';
        $client->is_active = true;

        $this->assertTrue($admin->canAccessPanel($panel));
        $this->assertFalse($inactiveAdmin->canAccessPanel($panel));
        $this->assertFalse($client->canAccessPanel($panel));
    }
}
