<?php

use App\Http\Controllers\AiController;
use Illuminate\Support\Facades\Route;

// Redirect home to login if not authenticated
Route::get('/', function () {
    return redirect()->route('login');
});

// Protect these routes with 'auth' middleware
Route::middleware(['auth'])->group(function () {
    Route::get('/app', [AiController::class, 'index'])->name('dashboard');
    Route::post('/rewrite', [AiController::class, 'rewrite']);
    Route::post('/save', [AiController::class, 'save'])->name('save');
    Route::delete('/delete/{id}', [AiController::class, 'destroy'])->name('delete');
});

require __DIR__.'/auth.php'; 