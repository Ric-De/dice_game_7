<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes of authentication using Passport
Route::controller(ApiController::class)->group(function() {
    Route::post('/players', 'register')/* ->name('user.register') */;
    Route::post('/login', 'login')->name('user.login');
});

Route::middleware('auth:api')->group( function () {
    Route::post('/logout', [ApiController::class, 'logout'])->name('user.logout');
});
