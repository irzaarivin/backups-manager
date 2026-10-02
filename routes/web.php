<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FinderController;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/', fn () => redirect()->route('finder'));
    Route::get('/finder', [FinderController::class, 'index'])->name('finder');
    Route::get('/api/preview/{path}', [FinderController::class, 'preview'])->where('path', '.*')->name('finder.preview');
    Route::get('/download/{path}', [FinderController::class, 'download'])->where('path', '.*')->name('finder.download');
});
