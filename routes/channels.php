<?php

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::routes(['middleware' => ['auth:sanctum']]);

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat.{receiverId}', function (User $user, $receiverId) {
    return (int) $user->id === (int) $receiverId;
});

Broadcast::channel('conversation.{conversationId}', function (User $user, $conversationId) {
    $conversation = Conversation::findOrFail($conversationId);

    return $user->id === $conversation->client_id || $user->id === $conversation->admin_id;
});
