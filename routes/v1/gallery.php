<?php

use App\Http\Controllers\api\GalleryController;
use Illuminate\Support\Facades\Route;

Route::get('gallery', [GalleryController::class, 'index']);
