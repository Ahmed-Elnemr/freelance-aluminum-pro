<?php

use App\Http\Controllers\api\AuthController;
use App\Service\ConfirmationController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'user-auth'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('activate', [ConfirmationController::class, 'activate']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('store-name', [AuthController::class, 'storeName']);
        Route::get('profile', [AuthController::class, 'profile']);
        Route::post('edite-profile', [AuthController::class, 'editeProfile']);
        Route::post('confirm-new-mobile', [AuthController::class, 'confirmMobileChange']);
        Route::get('logout', [AuthController::class, 'logout']);
        Route::delete('delete-account', [AuthController::class,'deleteAccount']);
    });
    Route::post('resend-code', [ConfirmationController::class, 'resendCode']);
});
//Route::group(['namespace' => 'Api', 'middleware' => 'api'], function () {
//    Route::post('client/signup', [AuthController::class,'clientSignup']);
//    Route::post('provider/signup', [AuthController::class,'providerSignup']);

//});
