<?php

namespace Tests\Feature;

use App\Enum\OrderStatusEnum;
use App\Http\Resources\NotificationDataResource;
use App\Models\Maintenance;
use App\Models\Order;
use App\Models\OrderRating;
use App\Models\User;
use App\Notifications\OrderCompletedNotification;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class OrderRatingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.connections.mysql.database', 'alumnium_pro_testing');
        DB::purge('mysql');
        DB::reconnect('mysql');

        if (DB::connection()->getDatabaseName() !== 'alumnium_pro_testing') {
            $this->fail('Refusing to run rating tests outside alumnium_pro_testing.');
        }

        $this->createSchema();
    }

    public function test_current_order_cannot_be_rated(): void
    {
        $user = User::factory()->create();
        $order = $this->createOrder($user, $this->createMaintenance(), OrderStatusEnum::Approved);

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/orders?status=current')
            ->assertOk()
            ->assertJsonFragment([
                'id' => $order->id,
                'can_rate' => false,
                'rating' => null,
            ]);

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/orders/'.$order->id)
            ->assertOk()
            ->assertJsonPath('data.can_rate', false)
            ->assertJsonPath('data.rating', null);
    }

    public function test_customer_can_rate_a_completed_order_once(): void
    {
        $user = User::factory()->create();
        $maintenance = $this->createMaintenance();
        $order = $this->createOrder($user, $maintenance, OrderStatusEnum::Completed);

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/orders?status=completed')
            ->assertOk()
            ->assertJsonFragment([
                'id' => $order->id,
                'can_rate' => true,
                'rating' => null,
            ]);

        $response = $this->rate($user, $order, [
            'stars' => 4,
            'comment' => 'الفني محترم وشغله نضيف',
            'tags' => ['on_time', 'on_time', 'quality'],
        ]);

        $response->assertCreated()
            ->assertJsonPath('status', true)
            ->assertJsonPath('message', 'شكراً لتقييمك!')
            ->assertJsonPath('data.stars', 4)
            ->assertJsonPath('data.comment', 'الفني محترم وشغله نضيف')
            ->assertJsonPath('data.tags', ['on_time', 'quality'])
            ->assertJsonMissingPath('data.user');

        $rating = OrderRating::query()->first();
        $this->assertNotNull($rating);
        $this->assertSame($order->id, $rating->order_id);
        $this->assertSame($user->id, $rating->user_id);
        $this->assertSame($maintenance->id, $rating->maintenance_id);
        $this->assertNotNull($rating->created_at?->toIso8601String());

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/orders/'.$order->id)
            ->assertOk()
            ->assertJsonPath('data.can_rate', false)
            ->assertJsonPath('data.rating.id', $rating->id)
            ->assertJsonPath('data.rating.stars', 4);

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/orders?status=completed')
            ->assertOk()
            ->assertJsonFragment([
                'id' => $order->id,
                'can_rate' => false,
            ]);

        $this->rate($user, $order, ['stars' => 5])
            ->assertStatus(409)
            ->assertJsonPath('status', false)
            ->assertJsonPath('code', 'order_already_rated')
            ->assertJsonPath('message', 'لقد قمت بتقييم هذا الطلب مسبقاً')
            ->assertJsonPath('data.id', $rating->id)
            ->assertJsonPath('data.stars', 4);
    }

    public function test_rating_with_stars_only_stores_empty_comment_and_tags(): void
    {
        $user = User::factory()->create();
        $order = $this->createOrder($user, $this->createMaintenance(), OrderStatusEnum::Completed);

        $this->rate($user, $order, [
            'stars' => 5,
            'comment' => '   ',
        ])->assertCreated()
            ->assertJsonPath('data.stars', 5)
            ->assertJsonPath('data.comment', null)
            ->assertJsonPath('data.tags', []);
    }

    public function test_customer_cannot_rate_another_customers_order(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $order = $this->createOrder($owner, $this->createMaintenance(), OrderStatusEnum::Completed);

        $this->rate($other, $order, ['stars' => 9])
            ->assertForbidden()
            ->assertJsonPath('status', false)
            ->assertJsonPath('code', 'order_not_ratable')
            ->assertJsonPath('message', 'لا يمكن تقييم هذا الطلب');

        $this->assertDatabaseCount('order_ratings', 0);
    }

    public function test_incomplete_order_cannot_be_rated(): void
    {
        $user = User::factory()->create();
        $order = $this->createOrder($user, $this->createMaintenance(), OrderStatusEnum::New);

        $this->rate($user, $order, ['stars' => 9])
            ->assertStatus(422)
            ->assertJsonPath('status', false)
            ->assertJsonPath('code', 'order_not_ratable')
            ->assertJsonMissingPath('errors');

        $this->assertDatabaseCount('order_ratings', 0);
    }

    public function test_missing_order_returns_not_found(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->withHeaders(['Accept' => 'application/json', 'lang' => 'ar'])
            ->post('/api/v1/orders/999999/rate', ['stars' => 5])
            ->assertNotFound();
    }

    public function test_invalid_rating_payload_is_rejected(): void
    {
        $user = User::factory()->create();
        $order = $this->createOrder($user, $this->createMaintenance(), OrderStatusEnum::Completed);

        $this->rate($user, $order, ['stars' => 0])
            ->assertStatus(422)
            ->assertJsonPath('status', false)
            ->assertJsonPath('message', 'التقييم يجب أن يكون بين 1 و 5')
            ->assertJsonPath('errors.stars.0', 'التقييم يجب أن يكون بين 1 و 5');

        $this->rate($user, $order, ['stars' => 6])
            ->assertStatus(422)
            ->assertJsonPath('errors.stars.0', 'التقييم يجب أن يكون بين 1 و 5');

        $this->rate($user, $order, [])
            ->assertStatus(422)
            ->assertJsonPath('errors.stars.0', 'التقييم يجب أن يكون بين 1 و 5');

        $this->rate($user, $order, [
            'stars' => 4,
            'tags' => ['unknown'],
        ])->assertStatus(422)
            ->assertJsonPath('message', 'أحد وسوم التقييم غير صالح');

        $this->rate($user, $order, [
            'stars' => 4,
            'comment' => str_repeat('أ', 501),
        ])->assertStatus(422)
            ->assertJsonPath('message', 'التعليق يجب ألا يتجاوز 500 حرف');

        $this->assertDatabaseCount('order_ratings', 0);
    }

    public function test_lang_header_translates_the_rating_message(): void
    {
        $user = User::factory()->create();
        $order = $this->createOrder($user, $this->createMaintenance(), OrderStatusEnum::Completed);

        $this->rate($user, $order, ['stars' => 3], [
            'lang' => 'en',
            'Accept-Language' => 'ar',
        ])->assertCreated()
            ->assertJsonPath('message', 'Thanks for your rating!');
    }

    public function test_completing_an_order_records_completed_at_and_sends_one_rating_reminder(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $order = $this->createOrder($user, $this->createMaintenance(), OrderStatusEnum::Approved);

        $order->update(['status' => OrderStatusEnum::Completed]);
        $order->refresh();

        $this->assertNotNull($order->completed_at);

        $order->update(['completed_at' => now()->subHours(25)]);

        $this->artisan('app:process-orders')->assertSuccessful();

        Notification::assertSentTo($user, OrderCompletedNotification::class);
        $this->assertTrue($order->fresh()->rating_reminder_sent);

        $this->artisan('app:process-orders')->assertSuccessful();

        Notification::assertSentTimes(OrderCompletedNotification::class, 1);
    }

    public function test_completed_orders_without_completed_at_are_not_reminded(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $order = $this->createOrder($user, $this->createMaintenance(), OrderStatusEnum::Completed);

        $this->assertNull($order->completed_at);

        $this->artisan('app:process-orders')->assertSuccessful();

        Notification::assertNothingSent();
        $this->assertFalse($order->fresh()->rating_reminder_sent);
    }

    public function test_completion_notification_opens_the_rating_screen(): void
    {
        $order = new Order;
        $order->id = 123;

        $notification = new OrderCompletedNotification($order);
        $payload = $notification->toDatabase(new User);

        App::setLocale('ar');
        $arabic = (new NotificationDataResource($payload))->toArray(request());

        $this->assertSame('order_rating', $arabic['type']);
        $this->assertSame(123, $arabic['model_id']);
        $this->assertSame('تم تنفيذ طلبك بنجاح 🎉', $arabic['title']);
        $this->assertSame('كيف كانت تجربتك؟ قيّم الخدمة في ثوانٍ', $arabic['message']);

        App::setLocale('en');
        $english = (new NotificationDataResource($payload))->toArray(request());

        $this->assertSame('Your order was completed successfully 🎉', $english['title']);
        $this->assertSame('How was your experience? Rate the service in seconds', $english['message']);
    }

    public function test_service_average_blends_legacy_ratings_and_order_ratings(): void
    {
        $maintenance = new Maintenance;
        $maintenance->ratings_count = 1;
        $maintenance->ratings_avg_rating = 2;
        $maintenance->order_ratings_count = 1;
        $maintenance->order_ratings_avg_stars = 5;

        $this->assertSame(2, $maintenance->combinedRatingCount());
        $this->assertSame(3.5, $maintenance->combinedRatingAverage());
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, string>  $headers
     */
    private function rate(User $user, Order $order, array $payload, array $headers = []): \Illuminate\Testing\TestResponse
    {
        return $this->actingAs($user, 'sanctum')
            ->withHeaders(array_merge([
                'Accept' => 'application/json',
                'lang' => 'ar',
            ], $headers))
            ->post('/api/v1/orders/'.$order->id.'/rate', $payload);
    }

    private function createMaintenance(): Maintenance
    {
        return Maintenance::query()->create([
            'name' => ['ar' => 'صيانة', 'en' => 'Maintenance'],
            'content' => ['ar' => 'تفاصيل', 'en' => 'Details'],
            'price' => 150,
            'final_price' => 120,
            'is_active' => true,
        ]);
    }

    private function createOrder(User $user, Maintenance $maintenance, OrderStatusEnum $status): Order
    {
        return Order::query()->create([
            'user_id' => $user->id,
            'maintenance_id' => $maintenance->id,
            'price' => 150,
            'final_price' => 120,
            'latitude' => 24.7136,
            'longitude' => 46.6753,
            'location_name' => 'Riyadh',
            'description' => ['ar' => 'وصف', 'en' => 'Description'],
            'internal_note' => 'note',
            'status' => $status,
            'is_active' => true,
            'scheduled_date' => now()->subDay()->toDateString(),
            'scheduled_time' => '10:00:00',
            'end_time' => '11:00:00',
        ]);
    }

    private function createSchema(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('order_ratings');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('maintenances');
        Schema::dropIfExists('media');
        Schema::dropIfExists('users');
        Schema::enableForeignKeyConstraints();

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('type')->default('client');
            $table->boolean('is_active')->default(true);
            $table->string('mobile')->nullable();
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('maintenances', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->json('content')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('final_price', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->unsignedBigInteger('maintenance_id')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('final_price', 10, 2)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('location_name')->nullable();
            $table->json('description')->nullable();
            $table->text('internal_note')->nullable();
            $table->string('status');
            $table->boolean('is_active')->default(true);
            $table->date('scheduled_date')->nullable();
            $table->time('scheduled_time')->nullable();
            $table->time('end_time')->nullable();
            $table->boolean('reminder_sent')->default(false);
            $table->boolean('finish_notified')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->boolean('rating_reminder_sent')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('order_ratings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->unique();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('maintenance_id')->nullable();
            $table->unsignedTinyInteger('stars');
            $table->text('comment')->nullable();
            $table->json('tags')->nullable();
            $table->boolean('is_hidden')->default(false);
            $table->timestamps();
        });

        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->morphs('model');
            $table->uuid()->nullable()->unique();
            $table->string('collection_name');
            $table->string('name');
            $table->string('file_name');
            $table->string('mime_type')->nullable();
            $table->string('disk');
            $table->string('conversions_disk')->nullable();
            $table->unsignedBigInteger('size');
            $table->json('manipulations');
            $table->json('custom_properties');
            $table->json('generated_conversions');
            $table->json('responsive_images');
            $table->unsignedInteger('order_column')->nullable()->index();
            $table->nullableTimestamps();
        });
    }
}
