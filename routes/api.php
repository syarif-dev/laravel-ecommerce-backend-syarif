<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::controller(AuthController::class)->group(function () {
    Route::post('/register-user', 'register')->name('register-user');
    Route::post('/register-seller', 'registerSeller')->name('register-seller');
    Route::post('/login', 'login')->name('login');
    Route::post('/logout', 'logout')->name('logout')->middleware('auth:sanctum');
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// seller
require __DIR__.'/seller.php';
// user or customer
require __DIR__.'/customer.php';
