<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/berita/{slug}', [ArticleController::class, 'show'])->name('articles.show');
Route::get('/kategori/{slug}', [CategoryController::class, 'show'])->name('categories.show');
Route::get('/cari', [SearchController::class, 'index'])->name('search');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/daftar', [AuthController::class, 'showRegister'])->name('register');
Route::post('/daftar', [AuthController::class, 'register']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Dashboard (perlu login)
Route::middleware('auth')->group(function () {
    // /dashboard mengarahkan admin ke dashboard admin, pembaca ke dashboard pembaca
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/pembaca', [DashboardController::class, 'user'])->name('user.dashboard');

    Route::patch('/profil/nama', [ProfilController::class, 'nama'])->name('profil.nama');
    Route::put('/profil/password', [ProfilController::class, 'password'])->name('profil.password');

    // Hanya admin
    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])
        ->middleware('admin')
        ->name('admin.dashboard');
});