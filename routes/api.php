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

Route::middleware('auth:api')->group(function(){

    //Admin routes
    Route::get('/players', [UserController::class, 'index'])->middleware('can:index')->name('user.index'); 
    
    Route::get('/players/ranking', [UserController::class, 'getRanking'])->middleware('can:getRanking')->name('user.ranking'); 
    
    Route::get('/players/ranking/loser', [UserController::class, 'getLoser'])->middleware('can:getLoser')->name('user.loser');
    
    Route::get('/players/ranking/winner', [UserController::class, 'getWinner'])->middleware('can:getWinner')->name('user.winner');
    
    
    //Player routes
    Route::post('/players/{id}/games/',[GameController::class,'play'])->middleware('can:play')->name('games.play');
    
    Route::get('players/{id}/games/',[GameController::class,'index_games'])->middleware('can:index_games')->name('games.index');
    
    Route::put('/players/{id}/', [UserController::class, 'update'])->middleware('can:update')->name('user.update');
    
    Route::delete('/players/{id}/games/',[GameController::class,'destroy'])->middleware('can:destroy')->name('games.destroy');
    

});
