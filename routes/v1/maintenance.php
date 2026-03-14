<?php

use App\Http\Controllers\api\FavoriteController;
use App\Http\Controllers\api\MaintenanceController;
use App\Http\Controllers\api\MaintenanceInspectionController;
use App\Http\Controllers\api\PaymentController;
use App\Http\Controllers\api\RateController;
use Illuminate\Support\Facades\Route;

Route::prefix('maintenances')->group(function () {
    Route::controller(MaintenanceController::class)->group(function () {
        Route::get('', 'index');
        Route::get('list', 'list');
        Route::get('{maintenance}', 'show');
        Route::post('search', 'search');
    });

    Route::middleware('auth:sanctum')->post('request-inspection', [MaintenanceInspectionController::class, 'requestInspection']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('favourite', [FavoriteController::class, 'toggle']);
        Route::post('rate/{maintenance}', [RateController::class, 'storeRate']);
        Route::get('my-favorites/get', [FavoriteController::class, 'myFavoriteMaintenances']);
    });

    Route::post('client/payment', [PaymentController::class, 'createPayment'])->middleware('auth:sanctum');
});
