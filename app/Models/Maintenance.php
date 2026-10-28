<?php

namespace App\Models;

use App\Traits\HasActiveScope;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Maintenance extends Model implements HasMedia
{
    use HasActiveScope, HasTranslations, InteractsWithMedia, SoftDeletes;

    public array $translatable = ['name', 'content'];

    protected $fillable = [
        'name',
        'content',
        'price',
        'final_price',
        'is_active',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('maintenances');
    }

    public function getMyRatingAttribute()
    {
        $user = auth('sanctum')->user();
        if (! $user) {
            return null;
        }

        return $this->ratings()->where('user_id', $user->id)->value('rating');
    }

    public function isFavorited(): bool
    {
        $user = auth('sanctum')->user();
        if (! $user) {
            return false;
        }

        return $this->favoritedByUsers()
            ->where('user_id', $user->id)
            ->exists();
    }

    public function averageRating(): float
    {
        return round($this->ratings()->avg('rating'), 1);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function orderRatings(): HasMany
    {
        return $this->hasMany(OrderRating::class);
    }

    public function combinedRatingCount(): int
    {
        return (int) ($this->ratings_count ?? 0) + (int) ($this->order_ratings_count ?? 0);
    }

    public function combinedRatingAverage(): float
    {
        $legacyCount = (int) ($this->ratings_count ?? 0);
        $legacyAverage = (float) ($this->ratings_avg_rating ?? 0);
        $orderCount = (int) ($this->order_ratings_count ?? 0);
        $orderAverage = (float) ($this->order_ratings_avg_stars ?? 0);
        $total = $legacyCount + $orderCount;

        if ($total === 0) {
            return 0.0;
        }

        return round((($legacyAverage * $legacyCount) + ($orderAverage * $orderCount)) / $total, 1);
    }

    public function ratings(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Rating::class, 'rateable');
    }

    public function favoritedByUsers(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Favorite::class, 'favouritable');
    }

    protected static function booted(): void
    {
        static::deleting(function (Maintenance $maintenance) {
            if ($maintenance->orders()->exists()) {
                Notification::make()
                    ->title(__('This maintenance cannot be deleted because it contains requests.'))
                    ->danger()
                    ->send();

                return false;
            }
        });
    }
}
