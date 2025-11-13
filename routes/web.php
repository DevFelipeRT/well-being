<?php

use App\Http\Controllers\CheckInController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DemoController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/**
 * Authentication via Breeze. Routes below require logged-in users.
 */

Route::get('/', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

/**
 * Dashboard
 */
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

/**
 * Authenticated area
 */
Route::middleware('auth')->group(function () {
    // Profile (Breeze default)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Well-being check-ins
    Route::resource('check-ins', CheckInController::class)
        ->parameters(['check-ins' => 'checkIn'])
        ->names([
            'index'   => 'check-ins.index',
            'create'  => 'check-ins.create',
            'store'   => 'check-ins.store',
            'show'    => 'check-ins.show',
            'edit'    => 'check-ins.edit',
            'update'  => 'check-ins.update',
            'destroy' => 'check-ins.destroy',
        ]);
});

Route::prefix('demo')->group(function () {
    Route::get('/start', [DemoController::class, 'start'])->name('demo.start');
    Route::post('/reset', [DemoController::class, 'reset'])->name('demo.reset');
    Route::post('/end', [DemoController::class, 'end'])->name('demo.end');
});

require __DIR__ . '/auth.php';
