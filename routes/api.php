<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HistoryController;

Route::middleware([
    'web',
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get(
        '/history/{entity}/{id}',
        [HistoryController::class, 'show'],
    )->where('entity', 'customer|product');

    // Route::get('/user', function (Request $request) {
    //     return $request->user();
    // });
});
