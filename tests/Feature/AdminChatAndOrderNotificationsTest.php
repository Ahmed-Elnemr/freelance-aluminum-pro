<?php

namespace Tests\Feature;

use App\Enum\OrderStatusEnum;
use App\Models\Order;
use App\Models\User;
use App\Notifications\AdminOrderStatusChangedNotification;
use App\Notifications\Channels\FcmChannel;
use App\Notifications\NewMessageFromClientNotification;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AdminChatAndOrderNotificationsTest extends TestCase
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

    public function test_client_message_notification_uses_database_and_fcm_channels(): void
    {
        Notification::fake();

        $admin = User::factory()->admin()->create();
        $client = User::factory()->create(['type' => 'client']);

        $admin->notify(new NewMessageFromClientNotification($client, 'Hello admin'));

        Notification::assertSentTo(
            $admin,
            NewMessageFromClientNotification::class,
            function (NewMessageFromClientNotification $notification, array $channels): bool {
                return in_array('database', $channels, true)
                    && in_array(FcmChannel::class, $channels, true);
            }
        );
    }

    public function test_admin_order_status_changed_notification_uses_database_and_fcm_channels(): void
    {
        Notification::fake();

        $admin = User::factory()->admin()->create();
        $order = new Order;
        $order->id = 15;
        $order->exists = true;
        $order->status = OrderStatusEnum::Approved;

        $admin->notify(new AdminOrderStatusChangedNotification($order, OrderStatusEnum::Approved));

        Notification::assertSentTo(
            $admin,
            AdminOrderStatusChangedNotification::class,
            function (AdminOrderStatusChangedNotification $notification, array $channels): bool {
                return in_array('database', $channels, true)
                    && in_array(FcmChannel::class, $channels, true);
            }
        );
    }
}
