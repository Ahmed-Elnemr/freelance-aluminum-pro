<?php

use App\Http\Controllers\api\AuthController;
use App\Service\ConfirmationController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'user-auth'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('activate', [ConfirmationController::class, 'activate']);
    Route::post('store-name', [AuthController::class, 'storeName'])
        ->middleware('auth:sanctum');
        Route::post('resend-code', [ConfirmationController::class, 'resendCode']);

});
//Route::group(['namespace' => 'Api', 'middleware' => 'api'], function () {
//    Route::post('client/signup', [AuthController::class,'clientSignup']);
//    Route::post('provider/signup', [AuthController::class,'providerSignup']);
//    Route::post('login', [AuthController::class,'login']);
//    Route::get('user/delete-account', [AuthController::class,'deleteAccount'])->middleware('auth:sanctum');
//    Route::get('user/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
//    Route::post('user/edite-profile', [AuthController::class, 'editeProfile'])->middleware('auth:sanctum');
//    Route::middleware('auth:sanctum')->group(function () {
//        Route::post('user/confirm-new-mobile', [AuthController::class, 'confirmMobileChange']);
//    });
//    Route::get('user/profile', [AuthController::class, 'profile'])->middleware('auth:sanctum');
//
//
//
//    Route::post('activate', [ConfirmationController::class,'activate']);
//    Route::post('resend_code', [ConfirmationController::class,'resend_code']);
//
//});
