<?php

use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->group(function () {
    Route::get('notifications', [\App\Http\Controllers\api\NotificationController::class, 'index']);
    Route::get('notifications/unread', [\App\Http\Controllers\api\NotificationController::class, 'unreadCount']);
    Route::delete('notifications/{uuid}', [\App\Http\Controllers\api\NotificationController::class, 'deleteNotification']);
    Route::delete('notifications', [\App\Http\Controllers\api\NotificationController::class, 'deleteAllNotifications']);

});
