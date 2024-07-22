<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Customer\OrderController;
use App\Http\Controllers\Api\Customer\StoreController;
use App\Http\Controllers\Api\Customer\AddressController;

Route::middleware('auth:sanctum')
    ->prefix('customer')
    ->name('customer.')
    ->group(function () {
        // address
        Route::apiResource('address', AddressController::class);

        // create order
        Route::post('order', [OrderController::class, 'createOrder']);
        // history order
        Route::get('order/histories', [OrderController::class, 'historyOrderBuyer']);

        //store
        Route::prefix('store')
            ->name('store.')
            ->group(function () {
                Route::get('/', [StoreController::class, 'index']);
                Route::get('/{id}/products', [StoreController::class, 'productByStore']);
                Route::get('/live-streaming', [StoreController::class, 'liveStreaming']);
            });
    });
