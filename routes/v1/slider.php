<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\SliderController;
use App\Service\ConfirmationController;
use Illuminate\Support\Facades\Route;

Route::get('slider/intro',[SliderController::class,'listIntro']);
