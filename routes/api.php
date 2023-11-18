<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function(){
    Route::post('login', 'login')->name('login');
    Route::post('register', 'register')->name('register');
    Route::any('logout', 'logout')->name('logout');
    Route::post('refresh', 'refresh')->name('refresh');
});

Route::apiResource('products', ProductController::class)
    ->only(['index', 'show']);

Route::middleware(['auth:api'])->group(function(){
    Route::controller(OrderController::class)->prefix('orders')->group(function(){
        Route::get('/', 'index')->name('orders.index');
        Route::get('/{order}', 'show')->name('orders.show');
        Route::post('create', 'create')->name('orders.create');
    });
});

