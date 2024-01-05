<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RentingGameController;
use App\Http\Controllers\GameController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// BCS3453 [PROJECT]-SEMESTER 2324/1
// Student ID: CB21132
// Student Name: SHATTHIYA GANES A/L SIVAKUMARAN


Route::get('/', function () {
    return view('auth.login');
});

Route::middleware(['auth', 'customerAccess'])->group(function () {

    Route::get('/dashboard', [GameController::class, 'getAllGames'])->name('dashboard');

    //Rent Game
    Route::get('/myGame', [RentingGameController::class, 'getRentedGames'])->name('myGame');
    Route::get('/myGameItem/{gameID}', [RentingGameController::class, 'getRentedGameItem'])->name('myGameItem');
    Route::post('/rentingGame/{gameID}', [RentingGameController::class, 'addRentingDetails'])->name('rentingGame');
    Route::get('/updateStatusMyGameItem/{gameID}', [RentingGameController::class, 'updateStatusRentedGameItem'])->name('updateMyGameItem');
    Route::get('/deleteStatusMyGameItem/{rentID}', [RentingGameController::class, 'deleteStatusRentedGameItem'])->name('deleteMyGameItem');



    Route::get('/rentGame/{gameID}', [RentingGameController::class, 'addRentingGameView'])->name('rentGame');
});

Route::middleware(['auth', 'adminAccess'])->group(function () {
    Route::get('/adminDashboard', [GameController::class, 'getAllGames'])->name('adminDashboard');

    // Game
    Route::get('/adminAddGame', [GameController::class, 'addGameView'])->name('addGame');

    Route::post('/adminAddGameFunc', [GameController::class, 'addGame'])->name('addGameInfo');

    Route::get('/adminDeleteGame/{gameID}', [GameController::class, 'deleteGameView'])->name('deleteGame');
});

Route::middleware(['auth'])->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Game
    Route::get('/viewItem/{gameID}', [GameController::class, 'getGameItem'])->name('viewItem');
});

require __DIR__ . '/auth.php';
