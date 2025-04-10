<?php

namespace App\Models;

use App\Traits\HasActiveScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class MaintenanceType extends Model
{
    use  HasTranslations,  SoftDeletes , HasActiveScope;
    public array $translatable = ['name'];

    protected $fillable = [
        'name',
        'is_active',
    ];

    #todo:relation
    public function orders(): HasMany
    {
        return $this->hasMany( Order::class);
    }
    #todo:# end relation #

}
