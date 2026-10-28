<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderRating extends Model
{
    /** @use HasFactory<\Database\Factories\OrderRatingFactory> */
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'maintenance_id',
        'stars',
        'comment',
        'tags',
        'is_hidden',
    ];

    protected $casts = [
        'tags' => 'array',
        'stars' => 'integer',
        'is_hidden' => 'boolean',
    ];

    /**
     * @return array{id: int, stars: int, comment: ?string, tags: list<string>, created_at: ?string}
     */
    public function toApiArray(): array
    {
        return [
            'id' => $this->id,
            'stars' => (int) $this->stars,
            'comment' => $this->comment,
            'tags' => array_values($this->tags ?? []),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function maintenance(): BelongsTo
    {
        return $this->belongsTo(Maintenance::class)->withTrashed();
    }
}
