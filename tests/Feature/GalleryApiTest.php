<?php

namespace Tests\Feature;

use App\Enum\GalleryTypeEnum;
use App\Filament\Resources\GalleryResource\Pages\ListGalleries;
use App\Models\Gallery;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class GalleryApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.connections.mysql.database', 'alumnium_pro_testing');
        app('db')->purge('mysql');
        app('db')->reconnect('mysql');

        Storage::fake('public');

        Schema::dropIfExists('media');
        Schema::dropIfExists('galleries');
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

        Schema::create('galleries', function (Blueprint $table) {
            $table->id();
            $table->json('title')->nullable();
            $table->string('type')->default('image');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
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

    public function test_gallery_endpoint_returns_active_items_in_sort_order(): void
    {
        $first = $this->makeGalleryItem('first.jpg', GalleryTypeEnum::Image, 1, true);
        $this->makeGalleryItem('hidden.jpg', GalleryTypeEnum::Image, 0, false);
        $second = $this->makeGalleryItem('clip.mp4', GalleryTypeEnum::Video, 2, true, true);

        $response = $this->getJson('/api/v1/gallery');

        $response->assertOk()
            ->assertJsonPath('status', true)
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.id', $first->id)
            ->assertJsonPath('data.0.type', 'image')
            ->assertJsonPath('data.1.id', $second->id)
            ->assertJsonPath('data.1.type', 'video')
            ->assertJsonPath('data.1.thumbnail', null);
    }

    public function test_gallery_endpoint_can_filter_by_type(): void
    {
        $this->makeGalleryItem('photo.jpg', GalleryTypeEnum::Image, 1, true);
        $this->makeGalleryItem('clip.mp4', GalleryTypeEnum::Video, 2, true, true);

        $this->getJson('/api/v1/gallery?type=video')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.type', 'video');
    }

    public function test_gallery_title_is_a_localized_string_not_an_array(): void
    {
        $gallery = $this->makeGalleryItem('photo.jpg', GalleryTypeEnum::Image, 1, true);
        $gallery->update([
            'title' => [
                'ar' => 'واجهة المكتب',
                'en' => 'Office facade',
            ],
        ]);

        $this->getJson('/api/v1/gallery', ['Accept-Language' => 'ar'])
            ->assertOk()
            ->assertJsonPath('data.0.title', 'واجهة المكتب');

        $this->getJson('/api/v1/gallery', ['Accept-Language' => 'en'])
            ->assertOk()
            ->assertJsonPath('data.0.title', 'Office facade');
    }

    public function test_gallery_endpoint_rejects_invalid_type(): void
    {
        $this->getJson('/api/v1/gallery?type=audio')
            ->assertStatus(422);
    }

    public function test_admin_can_see_gallery_records_in_dashboard(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();
        $gallery = $this->makeGalleryItem('show.jpg', GalleryTypeEnum::Image, 1, true);

        Livewire::actingAs($admin)
            ->test(ListGalleries::class)
            ->assertCanSeeTableRecords([$gallery]);
    }

    private function makeGalleryItem(
        string $fileName,
        GalleryTypeEnum $type,
        int $sortOrder,
        bool $isActive,
        bool $isVideo = false,
    ): Gallery {
        $gallery = Gallery::factory()->create([
            'type' => $type,
            'sort_order' => $sortOrder,
            'is_active' => $isActive,
        ]);

        $file = $isVideo
            ? UploadedFile::fake()->create($fileName, 200, 'video/mp4')
            : UploadedFile::fake()->image($fileName);

        $gallery->addMedia($file)->toMediaCollection('gallery');
        $gallery->syncTypeFromMedia();

        return $gallery->refresh();
    }
}
