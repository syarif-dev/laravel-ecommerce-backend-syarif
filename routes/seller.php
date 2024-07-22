<?php

use App\Http\Controllers\Api\Seller\CategoryController;
use App\Http\Controllers\Api\Seller\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')
    ->prefix('seller')
    ->name('seller.')
    ->group(function () {
        // categories
        Route::prefix('categories')
            ->name('categories.')
            ->group(function () {
                Route::get('/', [CategoryController::class, 'index'])->name('index');
                Route::post('/', [CategoryController::class, 'store'])->name('store');
        });

        // products
        // use api resource
        Route::apiResource('products', ProductController::class);
        // use group and prefix
        // Route::prefix('products')
        //     ->name('products.')
        //     ->group(function () {
        //         Route::get('/', [ProductController::class, 'index'])->name('index');
        //         Route::post('/', [ProductController::class, 'store'])->name('store');
        //         Route::put('/{id}', [ProductController::class, 'update'])->name('update');
        //         Route::delete('/{id}', [ProductController::class, 'destroy'])->name('destroy');
        // });
});
