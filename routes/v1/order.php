<?php

use App\Http\Controllers\api\OrderController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('orders', [OrderController::class, 'store']);
    Route::get('orders', [OrderController::class, 'index']);
    Route::get('orders/current', [OrderController::class, 'currentOrders']);
    Route::get('orders/expired', [OrderController::class, 'expiredOrders']);
    Route::get('orders/available-slots', [OrderController::class, 'availableSlots']);
    Route::get('orders/available-days', [OrderController::class, 'availableDays']);
    Route::post('orders/{order}/cancel', [OrderController::class, 'cancel']);
    Route::get('orders/{order}', [OrderController::class, 'show']);
});
