<?php

namespace App\Models;

use App\Enum\GalleryTypeEnum;
use App\Traits\HasActiveScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Gallery extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\GalleryFactory> */
    use HasActiveScope, HasFactory, HasTranslations, InteractsWithMedia, SoftDeletes;

    public array $translatable = ['title'];

    protected $fillable = [
        'title',
        'type',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'type' => GalleryTypeEnum::class,
        'is_active' => 'boolean',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('gallery')
            ->singleFile()
            ->useDisk('public');
    }

    public function isImage(): bool
    {
        return $this->resolvedType() === GalleryTypeEnum::Image;
    }

    public function isVideo(): bool
    {
        return $this->resolvedType() === GalleryTypeEnum::Video;
    }

    public function resolvedType(): GalleryTypeEnum
    {
        if ($this->type instanceof GalleryTypeEnum) {
            return $this->type;
        }

        return GalleryTypeEnum::tryFrom((string) $this->type) ?? GalleryTypeEnum::Image;
    }

    public function localizedTitle(?string $locale = null): string
    {
        $locale ??= app()->getLocale();
        $title = $this->getTranslation('title', $locale, true);

        if (is_array($title)) {
            return (string) ($title[$locale] ?? $title['ar'] ?? $title['en'] ?? '');
        }

        return (string) $title;
    }

    public function syncTypeFromMedia(): void
    {
        $media = $this->getFirstMedia('gallery');

        if (! $media) {
            return;
        }

        $mime = strtolower((string) $media->mime_type);
        $extension = strtolower((string) $media->extension);
        $isVideo = str_starts_with($mime, 'video/')
            || in_array($extension, ['mp4', 'mov', 'webm', 'qt'], true);

        $type = $isVideo ? GalleryTypeEnum::Video : GalleryTypeEnum::Image;

        if ($this->type !== $type) {
            $this->forceFill(['type' => $type])->saveQuietly();
        }
    }
}
