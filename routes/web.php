<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LocalizationController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\StatistikController;
use Illuminate\Support\Facades\Route;

// ── Publik ───────────────────────────────────────────────────────────────────
Route::get('/',               [ArticleController::class, 'index'])->name('articles.index');
Route::get('/berita',         [ArticleController::class, 'allNews'])->name('news.index');
Route::get('/tentang-kami',   [AboutController::class, 'index'])->name('about');
Route::get('/lang/{locale}',  [LocalizationController::class, 'switch'])->name('lang.switch');
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
Route::get('/berita/{slug}',  [ArticleController::class, 'show'])->name('articles.show');
Route::get('/kategori/{slug}',[CategoryController::class, 'show'])->name('categories.show');
Route::get('/cari',           [SearchController::class, 'index'])->name('search');
Route::get('/api/suggest',    [SearchController::class, 'suggest'])->name('search.suggest');

// ── Auth ─────────────────────────────────────────────────────────────────────
Route::get('/login',     [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',    [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::get('/masuk',     [AuthController::class, 'showLogin'])->name('login.id');
Route::post('/masuk',    [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::get('/daftar',    [AuthController::class, 'showRegister'])->name('register');
Route::post('/daftar',   [AuthController::class, 'register'])->middleware('throttle:6,1');
Route::get('/register',  [AuthController::class, 'showRegister'])->name('register.en');
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:6,1');
Route::post('/logout',   [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::get('/forgot-password',        [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password',       [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password',        [AuthController::class, 'resetPassword'])->name('password.update');

// ── Auth required ────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    // Redirect /dashboard ke dashboard yang sesuai role
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Dashboard User
    Route::get('/dashboard/pembaca', [DashboardController::class, 'user'])->name('user.dashboard');

    // Profil
    Route::patch('/profil/nama',     [ProfilController::class, 'nama'])->name('profil.nama');
    Route::put('/profil/password',   [ProfilController::class, 'password'])->name('profil.password');

    // Komentar — user login bisa kirim, admin bisa hapus
    Route::post('/berita/{slug}/komentar', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('/komentar/{comment}',   [CommentController::class, 'destroy'])->name('comments.destroy');

    // ── Admin only ───────────────────────────────────────────────────────────
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');

        // Kelola Komentar
        Route::get('/komentar', [CommentController::class, 'adminIndex'])->name('comments.index');

        // Kelola Kategori
        Route::get('/kategori',                    [CategoryController::class, 'adminIndex'])->name('categories.index');
        Route::get('/kategori/tambah',             [CategoryController::class, 'create'])->name('categories.create');
        Route::post('/kategori',                   [CategoryController::class, 'store'])->name('categories.store');
        Route::get('/kategori/{category}/edit',    [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/kategori/{category}',         [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/kategori/{category}',      [CategoryController::class, 'destroy'])->name('categories.destroy');

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

        // Laporan Statistik Berita
        Route::get('/laporan',               [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/export-pdf',    [LaporanController::class, 'exportPdf'])->name('laporan.pdf');
        Route::get('/laporan/export-excel',  [LaporanController::class, 'exportExcel'])->name('laporan.excel');
    });
});

// Fallback penyajian file storage publik untuk hosting tanpa dukungan symlink SSH
Route::get('/storage/{path}', function ($path) {
    $filePath = storage_path('app/public/' . $path);
    if (!file_exists($filePath)) {
        abort(404);
    }
    return response()->file($filePath);
})->where('path', '.*')->name('storage.local');

