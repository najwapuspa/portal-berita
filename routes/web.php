<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\StatistikController;
use Illuminate\Support\Facades\Route;

// ── Publik ───────────────────────────────────────────────────────────────────
Route::get('/',               [ArticleController::class, 'index'])->name('articles.index');
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
Route::get('/berita/{slug}',  [ArticleController::class, 'show'])->name('articles.show');
Route::get('/kategori/{slug}',[CategoryController::class, 'show'])->name('categories.show');
Route::get('/cari',           [SearchController::class, 'index'])->name('search');
Route::get('/api/suggest',    [SearchController::class, 'suggest'])->name('search.suggest');

// ── Auth ─────────────────────────────────────────────────────────────────────
Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',   [AuthController::class, 'login']);
Route::get('/daftar',   [AuthController::class, 'showRegister'])->name('register');
Route::post('/daftar',  [AuthController::class, 'register']);
Route::post('/logout',  [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ── Auth required ────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    // Redirect /dashboard ke dashboard yang sesuai role
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Dashboard User
    Route::get('/dashboard/pembaca', [DashboardController::class, 'user'])->name('user.dashboard');

    // Profil
    Route::patch('/profil/nama',     [ProfilController::class, 'nama'])->name('profil.nama');
    Route::put('/profil/password',   [ProfilController::class, 'password'])->name('profil.password');

    // ── Admin only ───────────────────────────────────────────────────────────
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');

        // Kelola Berita
        Route::get('/berita',              [ArticleController::class, 'adminIndex'])->name('articles.index');
        Route::get('/berita/tambah',       [ArticleController::class, 'create'])->name('articles.create');
        Route::post('/berita',             [ArticleController::class, 'store'])->name('articles.store');
        Route::get('/berita/{article}/edit',    [ArticleController::class, 'edit'])->name('articles.edit');
        Route::put('/berita/{article}',         [ArticleController::class, 'update'])->name('articles.update');
        Route::delete('/berita/{article}',      [ArticleController::class, 'destroy'])->name('articles.destroy');
        Route::patch('/berita/{article}/status',[ArticleController::class, 'toggleStatus'])->name('articles.toggle');

        // Kelola Pengguna
        Route::get('/pengguna',              [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/pengguna/tambah',       [AdminUserController::class, 'create'])->name('users.create');
        Route::post('/pengguna',             [AdminUserController::class, 'store'])->name('users.store');
        Route::get('/pengguna/{user}/edit',  [AdminUserController::class, 'edit'])->name('users.edit');
        Route::put('/pengguna/{user}',       [AdminUserController::class, 'update'])->name('users.update');
        Route::delete('/pengguna/{user}',    [AdminUserController::class, 'destroy'])->name('users.destroy');
        Route::patch('/pengguna/{user}/role',[AdminUserController::class, 'toggleRole'])->name('users.toggle');

        // Statistik
        Route::get('/statistik', [StatistikController::class, 'index'])->name('statistik');
    });
});
