<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    protected $fillable = [
        'user_id',
        'otp',
        'status',
        'expired_at',
    ];
    protected $casts = [
        'status' => 'boolean',
        'expired_at' => 'datetime:Y-m-d h:i a',
    ];


    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeCheckOtp($value)
    {
        return $value->where('expired_at','>',Carbon::now())->where('status',false);
    }
}
