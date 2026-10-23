<?php

use Illuminate\Support\Facades\Route;
use app\Http\Middleware\RoleMiddleware;
use App\Http\Controllers\Game\GameController;
use App\Http\Controllers\Assesment\AssesmentController;
use App\Http\Controllers\Assesment\AssesmentResultController;
use App\Http\Controllers\Battle\BattleController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Dashboard\AdminController;
use App\Http\Controllers\Dashboard\GuruController;
use App\Http\Controllers\Admin\VerifikasiGuruController;
use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Admin\SoalController;

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

// ROUTE ADMIN
Route::middleware(['auth', 'role:admin'])->group(function () {
    //------ DASHBOARD -------
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    //------ VERIFIKASI GURU -------
    Route::get('/admin/verifikasi-guru', [VerifikasiGuruController::class, 'index'])
    ->name('admin.verifikasi.index');
    Route::post('/admin/verifikasi-guru/{user}/terima', [VerifikasiGuruController::class, 'terima'])
    ->name('admin.verifikasi.terima');
    Route::post('/admin/verifikasi-guru/{user}/tolak', [VerifikasiGuruController::class, 'tolak'])
    ->name('admin.verifikasi.tolak');

    //------ KELOLA KATEGORI -------
    Route::get('/admin/kategori', [KategoriController::class, 'index'])
    ->name('admin.kategori.index');
    Route::post('/admin/kategori', [KategoriController::class, 'store'])
        ->name('admin.kategori.store');
    Route::post('/admin/kategori/{kategori}/ubah', [KategoriController::class, 'update'])
        ->name('admin.kategori.update');
    Route::post('/admin/kategori/{kategori}/hapus', [KategoriController::class, 'destroy'])
        ->name('admin.kategori.destroy');

    //------ KELOLA SOAL -------
    Route::get('/admin/soal', [SoalController::class, 'index'])
    ->name('admin.soal.index');
    Route::get('/admin/soal/tambah', [SoalController::class, 'create'])
    ->name('admin.soal.create');
    Route::post('/admin/soal', [SoalController::class, 'store'])
    ->name('admin.soal.store');
    Route::get('/admin/soal/{soal}/ubah', [SoalController::class, 'edit'])
    ->name('admin.soal.edit');
    Route::post('/admin/soal/{soal}/ubah', [SoalController::class, 'update'])
    ->name('admin.soal.update');
    Route::post('/admin/soal/{soal}/hapus', [SoalController::class, 'destroy'])
    ->name('admin.soal.destroy');
});

// ROUTE GURU
Route::middleware(['auth', 'role:guru'])->group(function () {
        //------ DASHBOARD -------
    Route::get('/guru/dashboard', [GuruController::class, 'dashboard'])->name('guru.dashboard');
});


Route::middleware(['auth'])->group(function(){
    //------ LOGOUT -------
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
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
