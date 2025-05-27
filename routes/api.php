<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HistoryController;
use App\Http\Controllers\Api\CustomerApiController;
use App\Http\Controllers\Api\ProductApiController;

Route::middleware([
    'web',
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get(
        '/history/{entity}/{id}',
        [HistoryController::class, 'show'],
    )->where('entity', 'customer|product|order');

    Route::get('/products', [ProductApiController::class, 'index']);
    Route::get('/customers', [CustomerApiController::class, 'index']);
});
