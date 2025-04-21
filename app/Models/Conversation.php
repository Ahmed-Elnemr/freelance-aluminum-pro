<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = [
        'client_id',
        'admin_id',
        'last_message_at',
        'is_closed',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'is_closed' => 'boolean',
    ];

    public function client(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function admin(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function messages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Message::class)->latest();
    }

    public function getOtherParticipantAttribute()
    {
        $user = auth()->user();
        return $user->id === $this->client_id ? $this->admin : $this->client;
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where(function($q) use ($userId) {
            $q->where('client_id', $userId)
                ->orWhere('admin_id', $userId);
        });
    }

    public function scopeBetween($query, $user1, $user2)
    {
        return $query->where(function($q) use ($user1, $user2) {
            $q->where('client_id', $user1)
                ->where('admin_id', $user2);
        })->orWhere(function($q) use ($user1, $user2) {
            $q->where('client_id', $user2)
                ->where('admin_id', $user1);
        });
    }


    public static function firstOrCreateBetween($user1, $user2)
    {
        $client = User::whereIn('id', [$user1, $user2])->where('type', 'client')->first();
        $admin = User::whereIn('id', [$user1, $user2])->where('type', 'admin')->first();

        if (!$client || !$admin) {
            throw new \Exception('Conversation must be between a client and an admin');
        }

        return self::firstOrCreate([
            'client_id' => $client->id,
            'admin_id' => $admin->id,
        ], [
            'last_message_at' => now(),
            'is_closed' => false,
        ]);
    }
}
