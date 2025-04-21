<?php

use App\Http\Controllers\api\ChatController;
use App\Http\Controllers\api\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('home',[HomeController::class,'home']);
Route::middleware('auth:sanctum')->post('send-message', [ChatController::class, 'sendMessage']);
