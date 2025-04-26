<?php

use App\Http\Controllers\api\SettingController;
use Illuminate\Support\Facades\Route;

Route::prefix('settings')->group(function () {
    Route::get('about-app-conditions', [SettingController::class, 'getSettings']);
});


