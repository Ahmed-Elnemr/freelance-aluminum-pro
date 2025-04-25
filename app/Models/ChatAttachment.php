<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatAttachment extends Model
{
    protected $fillable = ['message_id', 'file_path', 'mime_type'];

    public function message()
    {
        return $this->belongsTo(Message::class);
    }
}
