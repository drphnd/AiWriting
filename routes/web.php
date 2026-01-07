<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AiController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect root
Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

// =========================
// GUEST ROUTES
// =========================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// =========================
// AUTHENTICATED ROUTES
// =========================
Route::middleware('auth')->group(function () {

    // ---- AI APP ----
    Route::get('/app', [AiController::class, 'index'])->name('dashboard');

    // 🔥 IMPORTANT FIXES
    Route::post('/ai/rewrite', [AiController::class, 'rewrite'])->name('ai.rewrite');
    Route::post('/ai/save', [AiController::class, 'save'])->name('save');

    Route::delete('/ai/delete/{id}', [AiController::class, 'destroy'])->name('delete');

    // ---- HISTORY ----
    Route::get('/history', [AiController::class, 'history'])->name('history');
    Route::put('/history/{id}', [AiController::class, 'update'])->name('history.update');

    // ---- PROFILE ----
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ---- LOGOUT ----
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
