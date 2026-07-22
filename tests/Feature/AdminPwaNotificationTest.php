<?php

namespace Tests\Feature;

use App\Listeners\DispatchFilamentDatabaseNotificationsSent;
use App\Models\Order;
use App\Models\User;
use App\Models\UserDevice;
use App\Notifications\AdminOrderNotification;
use App\Notifications\Channels\FcmChannel;
use Filament\Facades\Filament;
use Filament\Notifications\Events\DatabaseNotificationsSent;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\MulticastSendReport;
use Mockery;
use Tests\TestCase;

class AdminPwaNotificationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.connections.mysql.database', 'alumnium_pro_testing');
        app('db')->purge('mysql');
        app('db')->reconnect('mysql');

        Schema::dropIfExists('notifications');
        Schema::dropIfExists('user_devices');
        Schema::dropIfExists('users');

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email', 191)->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('type')->default('client');
            $table->boolean('is_active')->default(true);
            $table->string('mobile')->nullable();
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('user_devices', function (Blueprint $table) {
            $table->id();
            $table->morphs('user');
            $table->string('uuid', 100)->nullable();
            $table->string('platform')->nullable();
            $table->string('token')->nullable();
            $table->timestamps();
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function test_admin_can_register_web_device_token(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->postJson(route('admin.device-token'), [
            'device_token' => 'web-fcm-token-123',
            'device_type' => 'web',
            'uuid' => 'browser-uuid-1',
        ]);

        $response->assertOk()
            ->assertJson(['message' => 'Device token registered.']);

        $this->assertDatabaseHas('user_devices', [
            'user_id' => $admin->id,
            'user_type' => User::class,
            'token' => 'web-fcm-token-123',
            'platform' => 'web',
            'uuid' => 'browser-uuid-1',
        ]);
    }

    public function test_guest_cannot_register_web_device_token(): void
    {
        $this->postJson(route('admin.device-token'), [
            'device_token' => 'web-fcm-token-123',
            'device_type' => 'web',
            'uuid' => 'browser-uuid-1',
        ])->assertUnauthorized();
    }

    public function test_admin_order_notification_uses_database_and_fcm_channels(): void
    {
        Notification::fake();

        $admin = User::factory()->admin()->create();
        $order = new Order;
        $order->id = 99;
        $order->exists = true;

        $admin->notify(new AdminOrderNotification($order, 'created'));

        Notification::assertSentTo(
            $admin,
            AdminOrderNotification::class,
            function (AdminOrderNotification $notification, array $channels): bool {
                return in_array('database', $channels, true)
                    && in_array(FcmChannel::class, $channels, true);
            }
        );
    }

    public function test_database_notification_dispatches_filament_realtime_event(): void
    {
        Event::fake([DatabaseNotificationsSent::class]);

        $admin = User::factory()->admin()->create();
        $order = new Order;
        $order->id = 42;
        $order->exists = true;

        $listener = new DispatchFilamentDatabaseNotificationsSent;
        $listener->handle(new NotificationSent(
            $admin,
            new AdminOrderNotification($order, 'created'),
            'database',
            []
        ));

        Event::assertDispatched(DatabaseNotificationsSent::class);
    }

    public function test_fcm_action_sends_to_all_device_tokens(): void
    {
        $admin = User::factory()->admin()->create();

        UserDevice::query()->create([
            'user_id' => $admin->id,
            'user_type' => User::class,
            'token' => 'token-one',
            'platform' => 'web',
            'uuid' => 'uuid-1',
        ]);

        UserDevice::query()->create([
            'user_id' => $admin->id,
            'user_type' => User::class,
            'token' => 'token-two',
            'platform' => 'android',
            'uuid' => 'uuid-2',
        ]);

        $messaging = Mockery::mock(Messaging::class);
        $messaging->shouldReceive('sendMulticast')
            ->once()
            ->withArgs(function ($message, $tokens): bool {
                $tokens = array_values($tokens);
                sort($tokens);

                return $tokens === ['token-one', 'token-two'];
            })
            ->andReturn(MulticastSendReport::withItems([]));

        $this->app->instance('firebase.messaging', $messaging);

        $order = new Order;
        $order->id = 7;
        $order->exists = true;

        Filament::setCurrentPanel(
            Filament::getPanel('admin')
        );

        $admin->notify(new AdminOrderNotification($order, 'created'));

        $this->assertDatabaseCount('notifications', 1);
    }
}
