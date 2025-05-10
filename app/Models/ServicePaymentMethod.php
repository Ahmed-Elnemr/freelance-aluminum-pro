<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServicePaymentMethod extends Model
{
    protected $fillable = [
        'user_id',
        'service_id',
        'paymentmethod'
    ];

    //todo: relation
    public function service(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo( User::class, 'user_id');
    }

}
