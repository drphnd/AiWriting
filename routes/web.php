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

// Redirect root ke login jika belum login, ke app jika sudah
Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// --- ROUTE KHUSUS TAMU (Login & Register Firebase) ---
Route::middleware(['guest'])->group(function () {
    // Arahkan ke AuthController buatan kita
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// --- ROUTE KHUSUS MEMBER (Sudah Login) ---
Route::middleware(['auth'])->group(function () {
    
    // Fitur Utama AI
    Route::get('/app', [AiController::class, 'index'])->name('dashboard');
    Route::post('/rewrite', [AiController::class, 'rewrite']);
    Route::post('/save', [AiController::class, 'save'])->name('save');
    Route::delete('/delete/{id}', [AiController::class, 'destroy'])->name('delete');
    
    // History
    Route::get('/history', [AiController::class, 'history'])->name('history');
    Route::put('/history/{id}', [AiController::class, 'update'])->name('history.update');

    // Profile (Bawaan Laravel Breeze - Simpan di SQLite saja tidak masalah untuk nama)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});