<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::apiResource('products', ProductController::class);
Route::apiResource('orders', OrderController::class);
