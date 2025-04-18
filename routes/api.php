<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group(['prefix' => 'v1'], function () {
    require __DIR__ . '/v1/auth.php';
    require __DIR__ . '/v1/slider.php';
    require __DIR__ . '/v1/home.php';
    require __DIR__ . '/v1/service.php';
});
