<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{

    protected $fillable = ['key', 'value',  'type'];

    protected $hidden = ['created_at', 'updated_at'];
}
