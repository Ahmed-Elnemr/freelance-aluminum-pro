<?php

use App\Http\Controllers\api\FavoriteController;
use App\Http\Controllers\api\PaymentController;
use App\Http\Controllers\api\RateController;
use App\Http\Controllers\api\ServiceController;
use Illuminate\Support\Facades\Route;

Route::prefix('services')->group(function () {
    Route::controller(ServiceController::class)->group(function () {
        Route::get('', 'mainServices');
        Route::get('main-services/{id}',[ServiceController::class,'ServicesByMian']);

//        Route::get('products', 'products');
//        Route::get('maintenance', 'maintenance');
        Route::get('{service}', 'show');
        Route::get('products/list', 'listProducts');
        Route::get('maintenance/list', 'listMaintenance');
        Route::post('search', 'search');

    });
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('favourite', [FavoriteController::class, 'toggle']);
        Route::post('rate/{service}', [RateController::class, 'storeRate']);
        Route::get('my-favorites/get', [FavoriteController::class, 'myFavoriteServices']);

    });
    Route::post('client/payment', [PaymentController::class, 'createPayment'])->middleware('auth:sanctum');

});


