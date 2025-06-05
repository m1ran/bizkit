<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Inertia\Inertia;

Route::middleware('throttle:60,1')
    ->group(function () {
        Route::get('/', function () {
            return Inertia::render('Home/Show', [
                'canLogin' => Route::has('login'),
                'canRegister' => Route::has('register'),
            ]);
        })->name('home');

        Route::middleware([
            'auth:sanctum',
            'verified',
            config('jetstream.auth_session'),
        ])->group(function () {
            Route::get('/dashboard', fn () => Inertia::render('Dashboard/Show'))->name('dashboard');

            Route::prefix('customers')
                ->name('customers.')
                ->group(function () {
                    Route::get('/', [CustomerController::class, 'index'])->name('index');
                    Route::post('/', [CustomerController::class, 'store'])->name('store');
                    Route::put('/{id}', [CustomerController::class, 'update'])->name('update');
                    Route::delete('/{id}', [CustomerController::class, 'destroy'])->name('delete');
                });

            Route::prefix('products')
                ->name('products.')
                ->group(function () {
                    Route::get('/', [ProductController::class, 'index'])->name('index');
                    Route::post('/', [ProductController::class, 'store'])->name('store');
                    Route::put('/{id}', [ProductController::class, 'update'])->name('update');
                    Route::delete('/{id}', [ProductController::class, 'destroy'])->name('delete');
                });

            Route::prefix('orders')
                ->name('orders.')
                ->group(function () {
                    Route::get('/', [OrderController::class, 'index'])->name('index');
                    Route::post('/', [OrderController::class, 'store'])->name('store');
                    Route::put('/{id}', [OrderController::class, 'update'])->name('update');
                    Route::delete('/{id}', [OrderController::class, 'destroy'])->name('delete');
                });
        });
    });
