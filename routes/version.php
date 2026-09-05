<?php

use App\Http\Controllers\VersionController;
use Illuminate\Support\Facades\Route;

// Public API
Route::get('/api/version', [VersionController::class, 'latest'])->name('api.version');
Route::get('/api/version/check', [VersionController::class, 'check'])->name('api.version.check');

// Authenticated web
Route::middleware(['auth', 'agency'])->group(function () {
    Route::get('/changelog', [VersionController::class, 'index'])->name('changelog');
    Route::get('/api/changelog', [VersionController::class, 'changelog'])->name('api.changelog');
});
