<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\HasActiveScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasActiveScope, SoftDeletes, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'status',
        'mobile',
        'new_mobile',
        'email',
        'password',
        'type',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    //todo:scope
    public function isClient(): bool
    {
        return $this->type === 'client';
    }

    // Check if the user is a provider
    public function isAdmin(): bool
    {
        return $this->type === 'admin';
    }
    //todo:scope # end


    //todo:relation
    public function devices(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(UserDevice::class, 'user', 'user_type', 'user_id')->latest();
    }

    public function otps(): HasMany
    {
        return $this->hasMany(Otp::class);
    }

    public function loginActivities(): HasMany
    {
        return $this->hasMany(LoginActivity::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class)->where('is_active', 1)
            ->with(['user', 'service']);
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    //todo: # end relation  #

}
