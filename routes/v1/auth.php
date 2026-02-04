<?php

use App\Http\Controllers\api\AuthController;
use App\Service\ConfirmationController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'user-auth'], function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    // Password Reset Routes
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);
    Route::post('resend-otp', [AuthController::class, 'resendOtp']);
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('store-name', [AuthController::class, 'storeName']);
        Route::get('profile', [AuthController::class, 'profile']);
        Route::post('edite-profile', [AuthController::class, 'editeProfile']);
        Route::post('change-password', [AuthController::class, 'changePassword']);
        Route::get('logout', [AuthController::class, 'logout']);
        Route::delete('delete-account', [AuthController::class,'deleteAccount']);
    });
});
