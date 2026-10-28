<?php

namespace App\Models;

use App\Enum\OrderStatusEnum;
use App\Traits\HasActiveScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Order extends Model implements HasMedia
{
    use HasActiveScope, HasTranslations, InteractsWithMedia, SoftDeletes;

    public array $translatable = ['description'];

    protected $fillable = [
        'maintenance_id',
        'price',
        'final_price',
        'user_id',
        'latitude',
        'longitude',
        'location_name',
        'description',
        'internal_note',
        'status',
        'is_active',
        'scheduled_date',
        'scheduled_time',
        'end_time',
        'reminder_sent',
        'finish_notified',
        'completed_at',
        'rating_reminder_sent',
    ];

    protected $casts = [
        'status' => OrderStatusEnum::class,
        'scheduled_date' => 'date',
        'completed_at' => 'datetime',
        'rating_reminder_sent' => 'boolean',
    ];

    public function getDateAttribute(): mixed
    {
        return $this->scheduled_date;
    }

    public function getTimeAttribute(): ?string
    {
        return $this->scheduled_time;
    }

    public function getFormattedTimeAttribute(): ?string
    {
        if (! $this->scheduled_time) {
            return null;
        }

        return \Illuminate\Support\Carbon::parse($this->scheduled_time)
            ->translatedFormat('h:i A');
    }

    public function getFormattedEndTimeAttribute(): ?string
    {
        if (! $this->end_time) {
            return null;
        }

        return \Illuminate\Support\Carbon::parse($this->end_time)
            ->translatedFormat('h:i A');
    }

    public function getServiceTypeNameAttribute(): ?string
    {
        return $this->maintenance?->getTranslation('name', app()->getLocale());
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('media')->useDisk('public');
        $this->addMediaCollection('sounds')->useDisk('public');
    }

    // todo: # end relation  #
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function maintenance(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Maintenance::class);
    }

    public function rating(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(OrderRating::class);
    }

    public function canBeRatedBy(?User $user): bool
    {
        if ($user === null) {
            return false;
        }

        return (int) $this->user_id === (int) $user->id
            && $this->status === OrderStatusEnum::Completed
            && $this->rating === null;
    }

    /**
     * @return array{can_rate: bool, rating: array{id: int, stars: int, comment: ?string, tags: list<string>, created_at: ?string}|null}
     */
    public function ratingApiPayload(?User $user): array
    {
        return [
            'can_rate' => $this->canBeRatedBy($user),
            'rating' => $this->rating?->toApiArray(),
        ];
    }

    protected static function booted(): void
    {
        static::updating(function (Order $order): void {
            if ($order->isDirty('status') && $order->status === OrderStatusEnum::Completed && $order->completed_at === null) {
                $order->completed_at = now();
            }
        });
    }
}
