<?php

use App\Http\Controllers\api\OrderController;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->group(function () {
    Route::post('orders', [OrderController::class, 'store']);
    Route::get('orders/current', [OrderController::class, 'currentOrders']);
    Route::get('orders/expired', [OrderController::class, 'expiredOrders']);
    Route::get('orders/{order}', [OrderController::class, 'show']);

});
