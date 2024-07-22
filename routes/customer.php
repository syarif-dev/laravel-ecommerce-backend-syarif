<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Customer\AddressController;

Route::middleware('auth:sanctum')
    ->prefix('customer')
    ->name('customer.')
    ->group(function () {
        // address
        Route::apiResource('address', AddressController::class);
    });
