<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/test', function (Request $request) {
    return 'nemr';
});

Route::group(['prefix' => 'v1'], function () {
    require __DIR__ . '/v1/auth.php';
});
