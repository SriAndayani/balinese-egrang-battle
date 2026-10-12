<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Game\GameController;
use App\Http\Controllers\Assesment\AssesmentController;
use App\Http\Controllers\Assesment\AssesmentResultController;
use App\Http\Controllers\Battle\BattleController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Dashboard\AdminController;
use App\Http\Controllers\Dashboard\GuruController;

// halaman welcome bawaan laravel
// Route::get('/', function () {
//     return view('welcome');
// });


// ------------------ AUTHENTICATION AREA ------------------
Route::middleware(['guest'])->group(function(){
    // ROUTE LOGIN
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.proses');

    // ROUTE REGISTER
    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.proses');
}); 

Route::middleware(['auth'])->group(function(){
    //------ ROUTE ADMIN -------
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    //------ ROUTE GURU -------
    Route::get('/guru/dashboard', [GuruController::class, 'dashboard'])->name('guru.dashboard');
});



// ================ GAMES AREA ================
Route::get('/', [GameController::class, 'index'])
    ->name('game.index');
// ------------ ASSESMENT VIEW ------------
Route::prefix('assesment')->name('assesment.')->group(function () {

    Route::get('/{room}', [AssesmentController::class, 'show'])
        ->name('show');

    Route::post('/{room}/start', [AssesmentController::class, 'start'])
        ->name('start');

    Route::get('/{room}/play', [AssesmentController::class, 'play'])
        ->name('play');

    Route::post('/{room}/answer', [AssesmentController::class, 'answer'])
        ->name('answer');

    Route::post('/{room}/next', [AssesmentController::class, 'next'])
        ->name('next');

    Route::get('/{room}/finish', [AssesmentController::class, 'finish'])
        ->name('finish');

    Route::get('/result/{result}', [AssesmentResultController::class, 'show'])
        ->name('result.show');
});

// ------------------ BATTLE VIEW ------------------
Route::prefix('battle')->name('battle.')->group(function () {

    Route::get('/{room}', [BattleController::class, 'show'])
        ->name('show');

    Route::post('/{room}/start', [BattleController::class, 'start'])
        ->name('start');

    Route::get('/{room}/play', [BattleController::class, 'play'])
        ->name('play');

    Route::post('/{room}/answer', [BattleController::class, 'answer'])
        ->name('answer');

    Route::get('/{room}/result', [BattleController::class, 'result'])
        ->name('result');
});
