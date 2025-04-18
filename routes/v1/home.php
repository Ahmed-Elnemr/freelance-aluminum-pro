<?php

use App\Http\Controllers\api\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('home',[HomeController::class,'home']);
