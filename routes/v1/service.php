<?php

use App\Http\Controllers\api\HomeController;
use App\Http\Controllers\api\ServiceController;
use Illuminate\Support\Facades\Route;

Route::get('services/products',[ServiceController::class,'products']);
Route::get('services/maintenance',[ServiceController::class,'maintenance']);
Route::get('services/{service}',[ServiceController::class,'show']);

