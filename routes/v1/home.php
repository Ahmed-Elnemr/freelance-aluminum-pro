<?php

use App\Http\Controllers\api\ChatController;
use App\Http\Controllers\api\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('home',[HomeController::class,'home']);
Route::middleware('auth:sanctum')->prefix('chat')->group(function () {
    Route::post('send-message', [ChatController::class, 'sendMessage']);
    Route::get('conversations', [ChatController::class, 'getConversations']);
    Route::get('conversations/messages', [ChatController::class, 'getMessages']);
    Route::post('conversations/{conversationId}/mark-read', [ChatController::class, 'markAsRead']);
});
