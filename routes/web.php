<?php

use App\Http\Controllers\AkunController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\PerizinanController;
use Illuminate\Support\Facades\Route;

// Redirect root ke beranda atau login
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('beranda')
        : redirect()->route('login');
});

// ============================================================
// Route Autentikasi (Guest Only)
// ============================================================
Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);

    // Registrasi akun baru
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);

    // Lupa password
    Route::get('/lupa-password', [ForgotPasswordController::class, 'create'])->name('password.request');
    Route::post('/lupa-password', [ForgotPasswordController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'resetCreate'])->name('password.reset');
    Route::post('/reset-password', [ForgotPasswordController::class, 'resetStore'])->name('password.update');
});

// ============================================================
// Route Terproteksi (Auth + Session Timeout)
// ============================================================
Route::middleware(['auth', 'session.timeout'])->group(function () {
    // Logout
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    // Beranda
    Route::get('/beranda', [BerandaController::class, 'index'])->name('beranda');

    // Pengaturan Akun
    Route::get('/akun/pengaturan', [AkunController::class, 'edit'])->name('akun.pengaturan');
    Route::put('/akun/pengaturan', [AkunController::class, 'update'])->name('akun.update');
    Route::put('/akun/password', [AkunController::class, 'updatePassword'])->name('akun.password');

    // Perizinan (untuk semua user yang sudah login)
    Route::get('/perizinan', [PerizinanController::class, 'index'])->name('perizinan.index');
    Route::get('/perizinan/{id}', [PerizinanController::class, 'show'])->name('perizinan.show');

    // Ajukan perizinan baru (hanya role pengguna)
    Route::middleware('role:pengguna')->group(function () {
        Route::get('/perizinan/ajukan/baru', [PerizinanController::class, 'create'])->name('perizinan.create');
        Route::post('/perizinan', [PerizinanController::class, 'store'])->name('perizinan.store');
    });

    // ============================================================
    // Route Admin (admin-pemda dan superadmin)
    // ============================================================
    Route::middleware('role:admin-pemda,superadmin')->prefix('admin')->name('admin.')->group(function () {
        // Manajemen Perizinan
        Route::get('/perizinan', [Admin\PerizinanController::class, 'index'])->name('perizinan.index');
        Route::get('/perizinan/{perizinan}', [Admin\PerizinanController::class, 'show'])->name('perizinan.show');
        Route::put('/perizinan/{perizinan}', [Admin\PerizinanController::class, 'update'])->name('perizinan.update');
    });

    // ============================================================
    // Route Superadmin (hanya superadmin)
    // ============================================================
    Route::middleware('role:superadmin')->prefix('admin')->name('admin.')->group(function () {
        // Manajemen Akun
        Route::get('/akun', [Admin\AkunController::class, 'index'])->name('akun.index');
        Route::post('/akun', [Admin\AkunController::class, 'store'])->name('akun.store');
        Route::put('/akun/{user}', [Admin\AkunController::class, 'update'])->name('akun.update');
        Route::put('/akun/{user}/password', [Admin\AkunController::class, 'updatePassword'])->name('akun.password');
        Route::delete('/akun/{user}', [Admin\AkunController::class, 'destroy'])->name('akun.destroy');
    });
});
