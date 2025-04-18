<?php

namespace App\Traits;
use Illuminate\Database\Eloquent\Builder;
trait HasActiveScope
{


    /**
     * Local scope for active items
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Local scope for inactive items
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    /**
     * Check if the model instance is inactive.
     */
    public function isInactive(): bool
    {
        return $this->is_active == true;
    }

}
