<?php

use App\Http\Controllers\AiController;
use App\Http\Controllers\ProfileController; // <--- ADD THIS IMPORT
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/app', [AiController::class, 'index'])->name('dashboard');
    Route::post('/rewrite', [AiController::class, 'rewrite']);
    Route::post('/save', [AiController::class, 'save'])->name('save');
    Route::delete('/delete/{id}', [AiController::class, 'destroy'])->name('delete');
    
    // History Routes
    Route::get('/history', [AiController::class, 'history'])->name('history');
    Route::put('/history/{id}', [AiController::class, 'update'])->name('history.update');

    // --- ADD THESE PROFILE ROUTES ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';