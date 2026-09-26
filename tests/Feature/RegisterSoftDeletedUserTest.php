<?php

namespace Tests\Feature;

use App\Models\User;
use App\Service\AuthService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class RegisterSoftDeletedUserTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.connections.mysql.database', 'alumnium_pro_testing');
        app('db')->purge('mysql');
        app('db')->reconnect('mysql');

        Schema::dropIfExists('personal_access_tokens');
        Schema::dropIfExists('user_devices');
        Schema::dropIfExists('users');

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email', 191)->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('type')->default('client');
            $table->boolean('status')->default(1);
            $table->boolean('is_active')->default(true);
            $table->string('mobile')->nullable();
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
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

        $this->mock(AuthService::class)
            ->shouldReceive('sendVerificationOtp');
    }

    public function test_register_restores_soft_deleted_user_instead_of_duplicate_insert(): void
    {
        $user = User::factory()->create([
            'email' => 'abdoshamss2005@gmail.com',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);
        $user->delete();

        $response = $this->postJson('/api/v1/user-auth/register', $this->registerPayload($user->email));

        $response->assertOk()
            ->assertJsonPath('data.need_token', true)
            ->assertJsonPath('data.user.email', $user->email);

        $this->assertSame(1, User::withTrashed()->where('email', $user->email)->count());

        $user->refresh();
        $this->assertNull($user->deleted_at);
        $this->assertNull($user->email_verified_at);
        $this->assertSame(0, (int) $user->is_active);
        $this->assertSame('abdo', $user->name);
    }

    public function test_register_rejects_existing_verified_email(): void
    {
        $user = User::factory()->create([
            'email' => 'verified@example.com',
            'email_verified_at' => now(),
        ]);

        $this->postJson('/api/v1/user-auth/register', $this->registerPayload($user->email), [
            'Accept-Language' => 'ar',
        ])->assertStatus(422)
            ->assertJsonPath('message', 'قيمة البريد الإلكتروني مستخدمة من قبل.');

        $this->postJson('/api/v1/user-auth/register', $this->registerPayload($user->email), [
            'Accept-Language' => 'en',
        ])->assertStatus(422)
            ->assertJsonPath('message', 'The email address has already been taken.');

        $user->refresh();
        $this->assertNotNull($user->email_verified_at);
        $this->assertSame(1, User::query()->where('email', $user->email)->count());
    }

    public function test_register_updates_unverified_user_and_resends_otp(): void
    {
        $user = User::factory()->unverified()->create([
            'email' => 'pending@example.com',
            'name' => 'old name',
            'is_active' => false,
        ]);

        $this->postJson('/api/v1/user-auth/register', $this->registerPayload($user->email))
            ->assertOk()
            ->assertJsonPath('data.need_token', true);

        $user->refresh();
        $this->assertSame('abdo', $user->name);
        $this->assertNull($user->email_verified_at);
        $this->assertSame(1, User::query()->where('email', $user->email)->count());
    }

    /**
     * @return array<string, string>
     */
    private function registerPayload(string $email): array
    {
        return [
            'name' => 'abdo',
            'email' => $email,
            'password' => '1234',
            'password_confirmation' => '1234',
            'mobile' => '0512341234',
            'device_token' => 'device-token',
            'device_type' => 'ios',
            'uuid' => '918F1133-BB67-7420-CF37-F0CC53B25B32',
        ];
    }
}
